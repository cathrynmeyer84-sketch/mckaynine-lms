<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Handler;
use App\Models\HandlerMessage;
use Illuminate\Http\Request;

class HandlerMessageController extends Controller
{
    public function index()
    {
        $messages = HandlerMessage::with(['handler', 'sentBy'])
            ->latest()->paginate(30);
        return view('admin.messages.index', compact('messages'));
    }

    public function create(Request $request)
    {
        $handlers = Handler::orderBy('first_name')->get();
        $preselectedHandler = $request->filled('handler_id')
            ? Handler::find($request->handler_id)
            : null;
        return view('admin.messages.create', compact('handlers', 'preselectedHandler'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'handler_ids'  => 'required|array|min:1',
            'handler_ids.*' => 'exists:handlers,id',
            'subject'      => 'required|string|max:255',
            'body'         => 'required|string',
        ]);

        foreach ($request->handler_ids as $handlerId) {
            HandlerMessage::create([
                'handler_id'      => $handlerId,
                'sent_by_user_id' => auth()->id(),
                'subject'         => $request->subject,
                'body'            => $request->body,
            ]);
        }

        $count = count($request->handler_ids);
        return redirect()->route('admin.messages.index')
            ->with('success', "Message sent to {$count} handler" . ($count !== 1 ? 's' : '') . ".");
    }
}
