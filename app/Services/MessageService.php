<?php

namespace App\Services;

use App\Models\{ClassDate, Conversation, ConversationParticipant, Message, MessageTemplate, DogClass, Enrolment, User, WeeklyContent};
use App\Services\PushNotificationService;

class MessageService
{
    // ── Merge tag resolution ────────────────────────────────────────────────

    public function resolveBlocks(array $blocks, array $context): array
    {
        $map = $this->buildMergeMap($context);

        return array_map(function ($block) use ($map, $context) {
            if (isset($block['content'])) {
                $block['content'] = strtr($block['content'], $map);
            }
            if (isset($block['title'])) {
                $block['title'] = strtr($block['title'], $map);
            }
            if (isset($block['caption'])) {
                $block['caption'] = strtr($block['caption'], $map);
            }
            if (isset($block['url'])) {
                $block['url'] = strtr($block['url'], $map);
            }
            if (isset($block['label'])) {
                $block['label'] = strtr($block['label'], $map);
            }
            // Auto-populate next_class block from context class if IDs not already set
            if ($block['type'] === 'next_class' && isset($context['class'])) {
                $class = $context['class'];
                if (empty($block['class_ids'])) {
                    $block['class_ids'] = $class->next_class_ids ?? [];
                }
                if (empty($block['class_type_ids'])) {
                    $block['class_type_ids'] = $class->next_class_type_ids ?? [];
                }
            }
            return $block;
        }, $blocks);
    }

    public function resolveSubject(string $subject, array $context): string
    {
        return strtr($subject, $this->buildMergeMap($context));
    }

    private function buildMergeMap(array $context): array
    {
        return array_filter([
            '{{handler_name}}'    => $context['handler']?->first_name ?? '',
            '{{dog_name}}'        => $context['dog']?->name ?? '',
            '{{class_name}}'      => $context['class']?->name ?? '',
            '{{class_location}}'  => $context['class']?->location ?? '',
            '{{class_start_date}}' => $context['class']?->start_date?->format('d M Y') ?? '',
            '{{class_start_time}}' => $context['class']?->start_time ?? '',
            '{{class_end_time}}'  => $context['class']?->end_time ?? '',
            '{{instructor_names}}' => $context['class']?->instructors->map(fn($i) => $i->first_name . ' ' . $i->last_name)->join(', ') ?? '',
            '{{week_number}}'     => isset($context['week_number']) ? (string) $context['week_number'] : '',
            '{{result_url}}'      => isset($context['enrolment']) ? '/my/classes/' . $context['enrolment']->id . '?tab=result' : '/my/achievements',
            '{{off_date}}'        => $context['off_date'] ?? '',
            '{{off_reason}}'      => $context['off_reason'] ?? '',
            '{{next_class_date}}' => $context['next_class_date'] ?? '',
        ]);
    }

    // ── Send from template to a single handler ──────────────────────────────

    public function sendTemplateToHandler(
        string $slug,
        User $handlerUser,
        array $context = [],
        ?int $adminUserId = null,
        ?int $classId = null
    ): Conversation {
        $template = MessageTemplate::where('slug', $slug)->first();

        $blocks  = $template ? $this->resolveBlocks($template->blocks, $context) : [['type' => 'text', 'content' => "Message: $slug"]];
        $subject = $template ? $this->resolveSubject($template->subject, $context) : $slug;

        $conversation = Conversation::create([
            'type'                => 'system',
            'subject'             => $subject,
            'class_id'            => $classId,
            'created_by_user_id'  => $adminUserId,
            'is_read_only'        => true,
            'template_slug'       => $slug,
        ]);

        $conversation->participants()->createMany([
            ['user_id' => $handlerUser->id],
        ]);

        Message::create([
            'conversation_id'  => $conversation->id,
            'sender_user_id'   => null,
            'blocks'           => $blocks,
        ]);

        $this->pushToUser($handlerUser, $subject);

        return $conversation;
    }

    // ── Send to all confirmed enrolments in a class ─────────────────────────

    public function broadcastToClass(
        DogClass $class,
        string $subject,
        array $blocks,
        int $senderUserId,
        string $type = 'class_announcement',
        bool $isReadOnly = true,
        ?string $templateSlug = null
    ): void {
        $enrolments = $class->enrolments()
            ->where('status', 'confirmed')
            ->with('handler.user')
            ->get();

        foreach ($enrolments as $enrolment) {
            $handlerUser = $enrolment->handler?->user;
            if (!$handlerUser) continue;

            $context = [
                'handler' => $enrolment->handler,
                'dog'     => $enrolment->dog,
                'class'   => $class,
            ];

            $resolvedBlocks  = $this->resolveBlocks($blocks, $context);
            $resolvedSubject = $this->resolveSubject($subject, $context);

            $conversation = Conversation::create([
                'type'               => $type,
                'subject'            => $resolvedSubject,
                'class_id'           => $class->id,
                'created_by_user_id' => $senderUserId,
                'is_read_only'       => $isReadOnly,
                'template_slug'      => $templateSlug,
            ]);

            $conversation->participants()->createMany([
                ['user_id' => $handlerUser->id],
            ]);

            Message::create([
                'conversation_id' => $conversation->id,
                'sender_user_id'  => null,
                'blocks'          => $resolvedBlocks,
            ]);

            $this->pushToUser($handlerUser, $resolvedSubject);
        }
    }

    // ── Send class content to all enrolled handlers ─────────────────────────

    public function broadcastClassContent(DogClass $class, ClassDate $classDate, WeeklyContent $content, int $adminUserId): void
    {
        $template = MessageTemplate::where('slug', 'class_content')->first();
        if (!$template) return;

        $weekNumber = $classDate->week_number
            ?? ClassDate::where('class_id', $class->id)
                ->where('is_off_week', false)
                ->where('date', '<=', $classDate->date)
                ->count();

        $practiceItems = array_values(array_filter(
            is_array($content->practice_checklist) ? $content->practice_checklist : []
        ));

        $contentBlock = [
            'type'           => 'class_content',
            'title'          => $content->title,
            'description'    => $content->description,
            'youtube_url'    => $content->youtube_url,
            'practice_items' => $practiceItems,
            'what_to_bring'  => $content->what_to_bring_next_week,
        ];

        $resolvedTemplateBlocks = array_map(fn($b) => $b['type'] === 'class_content' ? $contentBlock : $b, $template->blocks);

        $enrolments = $class->enrolments()
            ->where('status', 'confirmed')
            ->with(['handler.user', 'dog'])
            ->get();

        foreach ($enrolments as $enrolment) {
            $handlerUser = $enrolment->handler?->user;
            if (!$handlerUser) continue;

            $context = [
                'handler'     => $enrolment->handler,
                'dog'         => $enrolment->dog,
                'class'       => $class,
                'week_number' => $weekNumber,
            ];

            $resolvedBlocks  = $this->resolveBlocks($resolvedTemplateBlocks, $context);
            $resolvedSubject = $this->resolveSubject($template->subject, $context);

            $conversation = Conversation::create([
                'type'               => 'class_announcement',
                'subject'            => $resolvedSubject,
                'class_id'           => $class->id,
                'created_by_user_id' => $adminUserId,
                'is_read_only'       => true,
                'template_slug'      => 'class_content',
            ]);

            $conversation->participants()->createMany([
                ['user_id' => $handlerUser->id],
            ]);

            Message::create([
                'conversation_id' => $conversation->id,
                'sender_user_id'  => null,
                'blocks'          => $resolvedBlocks,
            ]);

            $this->pushToUser($handlerUser, $resolvedSubject);
        }
    }

    // ── Backfill past content to a single newly-enrolled handler ───────────

    public function backfillContentForEnrolment(Enrolment $enrolment): void
    {
        $template = MessageTemplate::where('slug', 'class_content')->first();
        if (!$template) return;

        $handlerUser = $enrolment->handler?->user;
        if (!$handlerUser) return;

        $class = $enrolment->dogClass;

        // Send all past sessions that have published content
        $pastDates = ClassDate::where('class_id', $class->id)
            ->where('date', '<', now())
            ->where('is_off_week', false)
            ->whereHas('weeklyContent', fn($q) => $q->where('is_published', true))
            ->with('weeklyContent')
            ->orderBy('date')
            ->get();

        foreach ($pastDates as $classDate) {
            $content = $classDate->weeklyContent;

            $weekNumber = $classDate->week_number
                ?? ClassDate::where('class_id', $class->id)
                    ->where('is_off_week', false)
                    ->where('date', '<=', $classDate->date)
                    ->count();

            $practiceItems = array_values(array_filter(
                is_array($content->practice_checklist) ? $content->practice_checklist : []
            ));

            $contentBlock = [
                'type'           => 'class_content',
                'title'          => $content->title,
                'description'    => $content->description,
                'youtube_url'    => $content->youtube_url,
                'practice_items' => $practiceItems,
                'what_to_bring'  => $content->what_to_bring_next_week,
            ];

            $resolvedTemplateBlocks = array_map(
                fn($b) => $b['type'] === 'class_content' ? $contentBlock : $b,
                $template->blocks
            );

            $context = [
                'handler'     => $enrolment->handler,
                'dog'         => $enrolment->dog,
                'class'       => $class,
                'week_number' => $weekNumber,
            ];

            $resolvedBlocks  = $this->resolveBlocks($resolvedTemplateBlocks, $context);
            $resolvedSubject = $this->resolveSubject($template->subject, $context);

            $conversation = Conversation::create([
                'type'          => 'class_announcement',
                'subject'       => $resolvedSubject,
                'class_id'      => $class->id,
                'is_read_only'  => true,
                'template_slug' => 'class_content',
            ]);

            $conversation->participants()->createMany([
                ['user_id' => $handlerUser->id],
            ]);

            Message::create([
                'conversation_id' => $conversation->id,
                'sender_user_id'  => null,
                'blocks'          => $resolvedBlocks,
            ]);
        }
    }

    // ── Send school-wide announcement ───────────────────────────────────────

    public function broadcastToSchool(string $subject, array $blocks, int $senderUserId): void
    {
        $activeUsers = User::whereHas('handler.enrolments', fn($q) => $q->where('status', 'confirmed'))
            ->get();

        foreach ($activeUsers as $user) {
            $conversation = Conversation::create([
                'type'               => 'school_announcement',
                'subject'            => $subject,
                'created_by_user_id' => $senderUserId,
                'is_read_only'       => true,
            ]);

            $conversation->participants()->createMany([
                ['user_id' => $user->id],
            ]);

            Message::create([
                'conversation_id' => $conversation->id,
                'sender_user_id'  => null,
                'blocks'          => $blocks,
            ]);

            $this->pushToUser($user, $subject);
        }
    }

    // ── Create a direct conversation (two-way) ──────────────────────────────

    public function createDirect(
        int $fromUserId,
        int $toUserId,
        string $subject,
        array $blocks,
        ?int $classId = null
    ): Conversation {
        $conversation = Conversation::create([
            'type'               => 'direct',
            'subject'            => $subject,
            'class_id'           => $classId,
            'created_by_user_id' => $fromUserId,
            'is_read_only'       => false,
        ]);

        $participantIds = array_unique([$fromUserId, $toUserId]);
        $conversation->participants()->createMany(
            array_map(fn($id) => ['user_id' => $id], $participantIds)
        );

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_user_id'  => $fromUserId,
            'blocks'          => $blocks,
        ]);

        if ($toUserId !== $fromUserId) {
            $toUser = User::find($toUserId);
            if ($toUser) $this->pushToUser($toUser, $subject);
        }

        return $conversation;
    }

    // ── Reply to an existing conversation ───────────────────────────────────

    public function reply(Conversation $conversation, int $senderUserId, array $blocks): Message
    {
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_user_id'  => $senderUserId,
            'blocks'          => $blocks,
        ]);

        // Notify all other participants
        $conversation->participants()
            ->where('user_id', '!=', $senderUserId)
            ->with('user')
            ->get()
            ->each(fn($p) => $this->pushToUser($p->user, 'New reply: ' . $conversation->subject));

        return $message;
    }

    // ── Unread count for a user ─────────────────────────────────────────────

    public function unreadCount(int $userId): int
    {
        return ConversationParticipant::where('user_id', $userId)
            ->whereHas('conversation.messages', function ($q) use ($userId) {
                $q->whereColumn('messages.created_at', '>',
                    \DB::raw('COALESCE((SELECT last_read_at FROM conversation_participants WHERE conversation_id = messages.conversation_id AND user_id = ' . $userId . '), \'1970-01-01\')')
                );
            })
            ->count();
    }

    // ── Push notification helper ────────────────────────────────────────────

    private function pushToUser(User $user, string $subject): void
    {
        try {
            app(PushNotificationService::class)->sendToUser(
                $user,
                'New Message',
                $subject,
                ['url' => '/my/inbox']
            );
        } catch (\Throwable) {
            // Push is best-effort
        }
    }
}
