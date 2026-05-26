<?php

namespace App\Console\Commands;

use App\Models\Enrolment;
use App\Services\InvoicesOnlineService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateMonthlyInvoices extends Command
{
    protected $signature   = 'billing:generate-monthly-invoices
                                {--month= : Year-month in Y-m format, e.g. 2026-06 (default: current month)}
                                {--dry-run : Show what would be invoiced without creating anything}';

    protected $description = 'Generate monthly invoices for all active ongoing class enrolments';

    public function handle(InvoicesOnlineService $io): int
    {
        $month  = $this->option('month') ?? now()->format('Y-m');
        $dryRun = $this->option('dry-run');
        $period = Carbon::parse($month . '-01');

        $this->info(($dryRun ? '[DRY RUN] ' : '') . "Generating monthly invoices for {$period->format('F Y')}...");

        if (!$io->isConfigured()) {
            $this->error('InvoicesOnline credentials are not configured in Branch Settings.');
            return Command::FAILURE;
        }

        // All confirmed enrolments in ongoing classes active this month
        $enrolments = Enrolment::where('status', 'confirmed')
            ->whereHas('dogClass', function ($q) use ($period) {
                $q->whereHas('classType', fn($q2) => $q2->where('duration_type', 'ongoing'))
                  ->where('start_date', '<=', $period->copy()->endOfMonth())
                  ->where('end_date', '>=', $period->copy()->startOfMonth());
            })
            ->with([
                'handler.accountHolder.linkedHandler',
                'handler.user',
                'dog',
                'dogClass.classType',
            ])
            ->get();

        if ($enrolments->isEmpty()) {
            $this->info('No active ongoing enrolments found for this period.');
            return Command::SUCCESS;
        }

        $this->info("Found {$enrolments->count()} enrolment(s). Grouping by billing account...");

        // Group by billing target so each billing account gets one combined invoice
        $grouped = [];

        foreach ($enrolments as $enrolment) {
            $handler        = $enrolment->handler;
            $billingHandler = $io->resolveBillingHandler($handler);
            $key            = 'h' . $billingHandler->id;

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'billing_handler' => $billingHandler,
                    'line_items'      => [],
                ];
            }

            $prodCode = $enrolment->dogClass->classType->io_prod_code ?? '';
            $price    = (float) ($enrolment->dogClass->classType?->course_price ?? $enrolment->dogClass->course_price ?? 0);
            $discount = $enrolment->dog?->multi_dog_discount ? 0.75 : 1.0;

            $grouped[$key]['line_items'][] = [
                'prod_code'   => $prodCode,
                'qty'         => 1,
                'description' => $enrolment->dogClass->name
                    . ' — ' . ($enrolment->dog?->name ?? 'Dog')
                    . ' (' . $period->format('F Y') . ')',
                'amount' => round($price * $discount, 2),
            ];
        }

        $success = 0;
        $failed  = 0;

        foreach ($grouped as $key => $group) {
            if (empty($group['line_items'])) continue;

            $billing = $group['billing_handler'];
            $count   = count($group['line_items']);

            if ($dryRun) {
                $total = array_sum(array_column($group['line_items'], 'amount'));
                $this->line("  Would invoice {$billing->full_name}: {$count} line(s) = R" . number_format($total, 2));
                $success++;
                continue;
            }

            $result = $io->createInvoice($billing, $group['line_items'], true);

            if ($result['success']) {
                $this->info("  ✓ Invoice {$result['invoice_nr']} → {$billing->full_name} ({$count} line" . ($count !== 1 ? 's' : '') . ')');
                $success++;
            } else {
                $this->error("  ✗ Failed for {$billing->full_name}: " . ($result['error'] ?? 'Unknown error'));
                $failed++;
            }
        }

        $this->newLine();
        $this->info($dryRun
            ? "Dry run complete. {$success} invoice(s) would be created."
            : "Done. {$success} invoice(s) created, {$failed} failed."
        );

        return $failed > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
