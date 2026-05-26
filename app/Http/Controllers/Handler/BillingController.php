<?php

namespace App\Http\Controllers\Handler;

use App\Http\Controllers\Controller;
use App\Services\InvoicesOnlineService;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index()
    {
        $handler  = auth()->user()->handler;

        abort_unless($handler, 403);

        $io       = new InvoicesOnlineService();
        $invoices = $io->getInvoices($handler);
        $hasIo    = (bool) $io->resolveClientId($handler);
        $pops     = $handler->billingPops()->latest()->get();

        // Billing contact info
        $contact  = $io->getBillingContact($handler);
        $ah       = $handler->accountHolder;

        return view('handler.billing.index', compact('handler', 'invoices', 'hasIo', 'pops', 'contact', 'ah'));
    }

    public function uploadPop(Request $request)
    {
        $handler = auth()->user()->handler;
        abort_unless($handler, 403);

        $request->validate([
            'invoice_reference' => 'nullable|string|max:50',
            'amount'            => 'nullable|numeric|min:0',
            'pop_file'          => 'required|file|mimetypes:image/jpeg,image/png,image/gif,application/pdf|max:10240',
        ]);

        $path = $request->file('pop_file')->store('billing/pops/' . $handler->id, 'public');

        $handler->billingPops()->create([
            'invoice_reference' => $request->invoice_reference,
            'amount'            => $request->amount,
            'file_path'         => $path,
        ]);

        return back()->with('success', 'Proof of payment uploaded. We\'ll confirm receipt shortly.');
    }
}
