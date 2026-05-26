<?php

namespace App\Http\Controllers;

use App\Models\AccountHolder;
use App\Models\AppNotification;

class AccountLinkController extends Controller
{
    public function approve(string $token)
    {
        $accountHolder = AccountHolder::where('link_token', $token)
            ->where('link_status', 'pending_approval')
            ->first();

        if (!$accountHolder) {
            return view('billing.link-response', [
                'status'  => 'expired',
                'message' => 'This link has already been used or has expired.',
            ]);
        }

        if ($accountHolder->link_expires_at && $accountHolder->link_expires_at->isPast()) {
            $accountHolder->update(['link_status' => 'rejected', 'link_token' => null]);
            return view('billing.link-response', [
                'status'  => 'expired',
                'message' => 'This link has expired. The handler has been set up with their own account.',
            ]);
        }

        $accountHolder->update([
            'link_status' => 'approved',
            'link_token'  => null,
        ]);

        // Notify Handler A (the one who submitted the enrolment)
        $handlerA = $accountHolder->handler;
        if ($handlerA?->user) {
            AppNotification::create([
                'user_id' => $handlerA->user_id,
                'type'    => 'billing',
                'title'   => 'Billing link approved',
                'message' => "{$accountHolder->name} has approved your billing link request. Your invoices will be added to their account.",
            ]);
        }

        return view('billing.link-response', [
            'status'  => 'approved',
            'message' => "You've approved the billing link. {$accountHolder->handler?->full_name}'s invoices will appear on your account.",
        ]);
    }

    public function decline(string $token)
    {
        $accountHolder = AccountHolder::where('link_token', $token)
            ->whereIn('link_status', ['pending_approval'])
            ->first();

        if (!$accountHolder) {
            return view('billing.link-response', [
                'status'  => 'expired',
                'message' => 'This link has already been used or has expired.',
            ]);
        }

        $accountHolder->update([
            'link_status' => 'rejected',
            'link_token'  => null,
        ]);

        // Notify Handler A
        $handlerA = $accountHolder->handler;
        if ($handlerA?->user) {
            AppNotification::create([
                'user_id' => $handlerA->user_id,
                'type'    => 'billing',
                'title'   => 'Billing link declined',
                'message' => "{$accountHolder->name} has declined your billing link request. Your account has been set up independently.",
            ]);
        }

        return view('billing.link-response', [
            'status'  => 'declined',
            'message' => "You've declined the billing link. The handler will be set up with their own separate account.",
        ]);
    }
}
