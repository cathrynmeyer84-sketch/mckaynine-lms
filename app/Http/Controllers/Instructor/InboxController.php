<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
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

        return view('instructor.inbox.index', compact('conversations'));
    }

    public function show(Conversation $conversation)
    {
        $userId = auth()->id();
        abort_unless(
            $conversation->participants()->where('user_id', $userId)->exists(),
            403
        );

        $conversation->load(['messages.sender', 'dogClass', 'participants.user']);
        $conversation->markReadFor($userId);

        return view('instructor.inbox.show', compact('conversation'));
    }

    public function reply(Request $request, Conversation $conversation)
    {
        $userId = auth()->id();
        abort_unless(
            $conversation->participants()->where('user_id', $userId)->exists() && !$conversation->is_read_only,
            403
        );

        $request->validate(['body' => 'required|string']);

        app(MessageService::class)->reply(
            $conversation,
            $userId,
            [['type' => 'text', 'content' => $request->body]]
        );

        return back()->with('success', 'Reply sent.');
    }
}
