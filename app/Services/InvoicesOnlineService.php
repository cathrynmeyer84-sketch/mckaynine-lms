<?php

namespace App\Services;

use App\Models\BranchSetting;
use App\Models\Handler;

class InvoicesOnlineService
{
    private string $baseUrl = 'https://www.invoicesonline.co.za/api/';
    private ?string $username;
    private ?string $password;
    private BranchSetting $branch;

    public function __construct()
    {
        $this->branch   = BranchSetting::current();
        $this->username = $this->branch->io_username;
        $this->password = $this->branch->io_password;
    }

    public function isConfigured(): bool
    {
        return !empty($this->username) && !empty($this->password);
    }

    // ---------------------------------------------------------------
    // Billing resolution
    // ---------------------------------------------------------------

    /**
     * Get the handler whose IO client ID should be billed.
     * For linked accounts: returns the linked (parent) handler.
     * For self or external: returns the handler itself.
     */
    public function resolveBillingHandler(Handler $handler): Handler
    {
        $handler->loadMissing(['accountHolder.linkedHandler']);
        $ah = $handler->accountHolder;

        if ($ah?->link_status === 'approved' && $ah->linkedHandler) {
            return $ah->linkedHandler;
        }

        return $handler;
    }

    /**
     * Get the stored IO client ID for a handler (following billing links).
     * Returns null if not yet synced.
     */
    public function resolveClientId(Handler $handler): ?string
    {
        return $this->resolveBillingHandler($handler)->invoicesonline_client_id ?: null;
    }

    /**
     * Get name + email to use for invoice creation when no client ID is stored.
     */
    public function getBillingContact(Handler $handler): array
    {
        $handler->loadMissing(['accountHolder.linkedHandler', 'user']);
        $ah = $handler->accountHolder;

        if ($ah?->link_status === 'approved' && $ah->linkedHandler) {
            $target = $ah->linkedHandler;
            return [
                'name'  => $target->full_name,
                'email' => $target->user?->email ?? $ah->email,
            ];
        }

        if ($ah) {
            return ['name' => $ah->name, 'email' => $ah->email];
        }

        return [
            'name'  => $handler->full_name,
            'email' => $handler->user?->email ?? '',
        ];
    }

    // ---------------------------------------------------------------
    // Core API operations
    // ---------------------------------------------------------------

    /**
     * Create an invoice.
     *
     * $lineItems = array of:
     *   ['prod_code' => string, 'description' => string, 'amount' => float, 'qty' => int (optional)]
     *
     * Returns: ['success' => bool, 'invoice_nr' => string, 'url' => string, 'error' => string]
     */
    public function createInvoice(Handler $handler, array $lineItems, bool $emailToClient = true): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'InvoicesOnline credentials not configured in Branch Settings.'];
        }

        $clientId = $this->resolveClientId($handler);
        $contact  = $this->getBillingContact($handler);

        $data = [];
        foreach (array_values($lineItems) as $i => $item) {
            $data[$i][0] = $item['prod_code']   ?? '';
            $data[$i][1] = $item['qty']         ?? 1;
            $data[$i][2] = $item['description'] ?? '';
            $data[$i][3] = number_format((float) ($item['amount'] ?? 0), 2, '.', '');
            $data[$i][4] = 'ZAR';
            $data[$i][5] = 0;    // vat_applies — disabled by default; enable once VAT-registered
            $data[$i][6] = 0.00; // vat_percentage
            $data[$i][7] = 0;    // amount_includes_vat
        }

        $params = [
            'data'          => $data,
            'EmailToClient' => $emailToClient ? 1 : 0,
        ];

        if ($clientId) {
            $params['ClientID'] = $clientId;
        } else {
            $params['client_invoice_name'] = $contact['name'];
            $params['client_email']        = $contact['email'];
        }

        $response = $this->post('AddNewInvoice.php', $params);

        if (!empty($response['invoice_nr'])) {
            // Opportunistically sync the client ID if we don't have one yet
            if (!$clientId) {
                $this->trySyncClientId($handler, $contact['name'], $contact['email']);
            }

            return [
                'success'     => true,
                'invoice_nr'  => $response['invoice_nr'],
                'url'         => $response['url']         ?? null,
                'document_id' => $response['document_id'] ?? null,
            ];
        }

        return [
            'success' => false,
            'error'   => $response['error'] ?? $response['message'] ?? 'Unknown error from InvoicesOnline.',
            'raw'     => $response,
        ];
    }

    /**
     * Fetch all invoices for a handler.
     * Returns a flat array of invoice field arrays.
     */
    public function getInvoices(Handler $handler): array
    {
        if (!$this->isConfigured()) return [];

        $clientId = $this->resolveClientId($handler);
        if (!$clientId) return [];

        $response = $this->post('getDocumentsByType_JSON.php', [
            'ClientID' => $clientId,
            'type'     => 'invoices',
        ]);

        if (!is_array($response)) return [];

        // Flatten nested [client_id][doc_id][field] → flat array of invoices
        $invoices = [];
        foreach ($response as $clientData) {
            if (!is_array($clientData)) continue;
            foreach ($clientData as $docData) {
                if (is_array($docData)) {
                    $invoices[] = $docData;
                }
            }
        }

        // Sort newest first
        usort($invoices, fn($a, $b) => strcmp($b['date'] ?? '', $a['date'] ?? ''));

        return $invoices;
    }

    /**
     * Record a payment against a client's account in IO.
     */
    public function recordPayment(
        Handler $handler,
        float $amount,
        string $date,
        string $method = 'EFT',
        string $reference = ''
    ): array {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'InvoicesOnline credentials not configured.'];
        }

        $clientId = $this->resolveClientId($handler);
        if (!$clientId) {
            return [
                'success' => false,
                'error'   => 'No InvoicesOnline client ID for this handler. Create at least one invoice first.',
            ];
        }

        $params = [
            'ClientID'        => $clientId,
            'PaymentDate'     => $date,
            'PaymentAmount'   => number_format($amount, 2, '.', ''),
            'PaymentMethod'   => $method,
            'ReferenceNumber' => $reference,
        ];

        $response = $this->post('AddNewPayment.php', $params);

        if (isset($response['url']) || isset($response['invoice_nr'])) {
            return ['success' => true];
        }

        return [
            'success' => false,
            'error'   => $response['error'] ?? $response['message'] ?? 'Unknown error.',
        ];
    }

    /**
     * Manually set the IO client ID on the billing target for a handler.
     */
    public function setClientId(Handler $handler, string $clientId): void
    {
        $this->resolveBillingHandler($handler)->update(['invoicesonline_client_id' => $clientId]);
    }

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------

    /**
     * After a first invoice creation (no ClientID), try to look up and save the IO client ID.
     * GetClientID requires ClientBranchName — if this fails, that's fine; admin can set it manually.
     */
    private function trySyncClientId(Handler $handler, string $name, string $email): void
    {
        try {
            $response = $this->post('GetClientID.php', [
                'ClientName'       => $name,
                'ClientEmail'      => $email,
                'ClientBranchName' => $this->branch->branch_name ?? '',
            ]);

            $clientId = null;
            if (is_numeric($response)) {
                $clientId = (string) $response;
            } elseif (is_array($response) && isset($response['client_id']) && is_numeric($response['client_id'])) {
                $clientId = (string) $response['client_id'];
            }

            if ($clientId) {
                $this->resolveBillingHandler($handler)->update(['invoicesonline_client_id' => $clientId]);
            }
        } catch (\Throwable) {
            // Best-effort — not critical. Admin can enter the ID manually.
        }
    }

    private function post(string $endpoint, array $params): mixed
    {
        $params['username'] = $this->username;
        $params['password'] = $this->password;

        $url = $this->baseUrl . $endpoint . '?apiformat=json';

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($params),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_SSL_VERIFYPEER => false, // As per IO documentation
            CURLOPT_TIMEOUT        => 15,
        ]);

        $raw      = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($raw === false || $httpCode >= 500) {
            return ['error' => 'InvoicesOnline API unreachable (HTTP ' . $httpCode . ').'];
        }

        return json_decode($raw, true) ?? $raw;
    }
}
