<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{BillingPop, Handler};
use App\Services\InvoicesOnlineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BillingController extends Controller
{
    /**
     * List all unreviewed proof-of-payment uploads.
     */
    public function pendingPops()
    {
        $pops = BillingPop::with(['handler'])
            ->where('is_reviewed', false)
            ->latest()
            ->get();

        return view('admin.billing.pops', compact('pops'));
    }

    /**
     * Mark a POP as reviewed.
     */
    public function reviewPop(BillingPop $billingPop)
    {
        $billingPop->update([
            'is_reviewed' => true,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'POP marked as reviewed.');
    }

    /**
     * Download a POP file (private storage).
     */
    public function downloadPop(BillingPop $billingPop)
    {
        abort_unless(Storage::disk('private')->exists($billingPop->file_path), 404);
        return Storage::disk('private')->download($billingPop->file_path);
    }

    /**
     * Record a payment in InvoicesOnline for a handler.
     */
    public function recordPayment(Request $request, Handler $handler)
    {
        $data = $request->validate([
            'amount'    => 'required|numeric|min:0.01',
            'date'      => 'required|date',
            'method'    => 'nullable|string|max:50',
            'reference' => 'nullable|string|max:100',
        ]);

        $io     = new InvoicesOnlineService();
        $result = $io->recordPayment(
            $handler,
            (float) $data['amount'],
            $data['date'],
            $data['method'] ?? 'EFT',
            $data['reference'] ?? ''
        );

        if ($result['success']) {
            return back()->with('success', 'Payment of R' . number_format($data['amount'], 2) . ' recorded in InvoicesOnline.');
        }

        return back()->with('error', 'Payment failed: ' . ($result['error'] ?? 'Unknown error'));
    }

    /**
     * Manually create an invoice for a handler (admin-triggered).
     */
    public function createInvoice(Request $request, Handler $handler)
    {
        $data = $request->validate([
            'prod_code'   => 'required|string|max:100',
            'description' => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0.01',
            'qty'         => 'nullable|integer|min:1',
            'email'       => 'nullable|boolean',
        ]);

        $io     = new InvoicesOnlineService();
        $result = $io->createInvoice($handler, [[
            'prod_code'   => $data['prod_code'],
            'description' => $data['description'],
            'amount'      => (float) $data['amount'],
            'qty'         => $data['qty'] ?? 1,
        ]], (bool) ($data['email'] ?? true));

        if ($result['success']) {
            return back()->with('success', 'Invoice ' . $result['invoice_nr'] . ' created in InvoicesOnline.');
        }

        return back()->with('error', 'Invoice failed: ' . ($result['error'] ?? 'Unknown error'));
    }

    /**
     * Save the IO client ID for a handler (admin manual override).
     */
    public function saveClientId(Request $request, Handler $handler)
    {
        $data = $request->validate([
            'invoicesonline_client_id' => 'nullable|string|max:100',
        ]);

        $io = new InvoicesOnlineService();
        if ($data['invoicesonline_client_id']) {
            $io->setClientId($handler, $data['invoicesonline_client_id']);
        } else {
            $io->resolveBillingHandler($handler)->update(['invoicesonline_client_id' => null]);
        }

        return back()->with('success', 'InvoicesOnline client ID updated.');
    }
}
