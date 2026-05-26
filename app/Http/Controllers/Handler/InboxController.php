<?php

namespace App\Http\Controllers\Handler;

use App\Http\Controllers\Controller;
use App\Models\{Conversation, User};
use App\Services\MessageService;
use Illuminate\Http\Request;

class InboxController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $conversations = Conversation::forUser($userId)
            ->with(['latestMessage', 'participants.user', 'dogClass'])
            ->latest()
            ->get()
            ->map(function ($conv) use ($userId) {
                $conv->is_unread = $conv->isUnreadFor($userId);
                return $conv;
            })
            ->sortByDesc(fn($c) => $c->latestMessage?->created_at ?? $c->created_at);

        return view('handler.inbox.index', compact('conversations'));
    }

    public function show(Conversation $conversation)
    {
        $userId = auth()->id();
        abort_unless(
            $conversation->participants()->where('user_id', $userId)->exists(),
            403
        );

        $conversation->load(['messages.sender', 'dogClass.scheduledDates', 'participants.user']);
        $conversation->markReadFor($userId);

        return view('handler.inbox.show', compact('conversation'));
    }

    public function create()
    {
        return view('handler.inbox.compose');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'body'    => 'required|string',
        ]);

        $adminUser = User::where('role', 'admin')->first();

        if (!$adminUser) {
            return back()->with('error', 'Could not reach admin — please try again.');
        }

        app(MessageService::class)->createDirect(
            auth()->id(),
            $adminUser->id,
            $request->subject,
            [['type' => 'text', 'content' => $request->body]]
        );

        return redirect()->route('handler.inbox.index')->with('success', 'Message sent.');
    }

    public function reply(Request $request, Conversation $conversation)
    {
        $userId = auth()->id();
        abort_unless($conversation->participants()->where('user_id', $userId)->exists(), 403);
        abort_if($conversation->is_read_only, 403);

        $request->validate(['body' => 'required|string']);

        app(MessageService::class)->reply(
            $conversation,
            $userId,
            [['type' => 'text', 'content' => $request->body]]
        );

        return redirect()->route('handler.inbox.show', $conversation)->with('success', 'Reply sent.');
    }
}
