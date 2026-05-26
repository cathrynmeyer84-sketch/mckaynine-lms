<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Conversation, DogClass, Handler, Instructor, MessageTemplate, User};
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
            ->paginate(30);

        return view('admin.inbox.index', compact('conversations', 'userId'));
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

        return view('admin.inbox.show', compact('conversation'));
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

    public function create(Request $request)
    {
        $handlers    = Handler::with('user')->orderBy('first_name')->get();
        $instructors = Instructor::where('is_active', true)->orderBy('first_name')->get();
        $classes     = DogClass::where('end_date', '>=', now())->orderBy('start_date')->get();
        $templates   = MessageTemplate::orderBy('name')->get();
        $preselect   = $request->query('handler_id');

        return view('admin.inbox.compose', compact('handlers', 'instructors', 'classes', 'templates', 'preselect'));
    }

    public function templates()
    {
        $templates = MessageTemplate::orderBy('name')->get();
        return view('admin.inbox.templates.index', compact('templates'));
    }

    public function editTemplate(MessageTemplate $template)
    {
        return view('admin.inbox.templates.edit', compact('template'));
    }

    public function updateTemplate(Request $request, MessageTemplate $template)
    {
        $request->validate([
            'subject'           => 'required|string|max:255',
            'blocks.*.content'  => 'nullable|string',
            'blocks.*.label'    => 'nullable|string|max:200',
            'blocks.*.url'      => 'nullable|string|max:500',
        ]);

        $blocks = $template->blocks;

        foreach ($blocks as $i => &$block) {
            if ($block['type'] === 'text' && isset($request->input('blocks')[$i]['content'])) {
                $block['content'] = $request->input('blocks')[$i]['content'];
            }
            if ($block['type'] === 'button') {
                if (isset($request->input('blocks')[$i]['label'])) {
                    $block['label'] = $request->input('blocks')[$i]['label'];
                }
                if (isset($request->input('blocks')[$i]['url'])) {
                    $block['url'] = $request->input('blocks')[$i]['url'];
                }
            }
        }
        unset($block);

        $template->update([
            'subject' => $request->subject,
            'blocks'  => $blocks,
        ]);

        return back()->with('success', 'Template saved.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'recipient_type' => 'required|in:handler,class,school,instructor',
            'subject'        => 'required|string|max:255',
            'body'           => 'required|string',
        ]);

        $blocks = [['type' => 'text', 'content' => $request->body]];

        if ($request->image_path) {
            $blocks[] = ['type' => 'image', 'path' => $request->image_path, 'caption' => ''];
        }

        $svc = app(MessageService::class);

        match ($request->recipient_type) {
            'handler' => $svc->createDirect(
                auth()->id(),
                User::findOrFail($request->handler_user_id)->id,
                $request->subject,
                $blocks
            ),
            'class' => $svc->broadcastToClass(
                DogClass::findOrFail($request->class_id),
                $request->subject,
                $blocks,
                auth()->id(),
                'class_announcement',
                false
            ),
            'school' => $svc->broadcastToSchool($request->subject, $blocks, auth()->id()),
            'instructor' => $svc->createDirect(
                auth()->id(),
                User::findOrFail($request->instructor_user_id)->id,
                $request->subject,
                $blocks
            ),
        };

        return redirect()->route('admin.inbox.index')->with('success', 'Message sent.');
    }
}
