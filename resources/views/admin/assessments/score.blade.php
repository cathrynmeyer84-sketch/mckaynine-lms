<x-app-layout :title="'Score Assessment'">
<div class="page-content">

    <div class="page-header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.assessments.show', $assessmentRequest) }}" class="text-gray-400 hover:text-navy">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="page-title">Assessment Score Sheet</h1>
                <p class="page-subtitle">
                    {{ $assessmentRequest->handler?->first_name }} {{ $assessmentRequest->handler?->last_name }}
                    &middot; {{ $assessmentRequest->dog?->name }}
                    &middot; {{ now()->format('d M Y') }}
                </p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.assessments.score.store', $assessmentRequest) }}"
        x-data="{
            scores: {
                step1: {{ $existingScore?->step1_score ?? 'null' }},
                step2: {{ $existingScore?->step2_score ?? 'null' }},
                step3: {{ $existingScore?->step3_score ?? 'null' }},
                step4: {{ $existingScore?->step4_score ?? 'null' }},
                step5: {{ $existingScore?->step5_score ?? 'null' }},
                step6: {{ $existingScore?->step6_score ?? 'null' }},
                step7: {{ $existingScore?->step7_score ?? 'null' }},
            },
            step7Skipped: {{ $existingScore?->step7_skipped ? 'true' : 'false' }},
            overrideOutcome: {{ $existingScore?->final_outcome ? 'true' : 'false' }},
            get validScores() {
                let s = [this.scores.step1, this.scores.step2, this.scores.step3,
                         this.scores.step4, this.scores.step5, this.scores.step6];
                if (!this.step7Skipped) s.push(this.scores.step7);
                return s.filter(v => v !== null);
            },
            get recommendedOutcome() {
                if (this.validScores.length === 0) return null;
                const avg = this.validScores.reduce((a,b) => a+b, 0) / this.validScores.length;
                if (avg <= 2.5) return 'group_class';
                if (avg <= 3.5) return 'private_lessons';
                return 'behaviourist';
            },
            get averageScore() {
                if (this.validScores.length === 0) return null;
                return (this.validScores.reduce((a,b) => a+b, 0) / this.validScores.length).toFixed(1);
            },
            outcomeLabel(o) {
                const labels = { group_class: 'Group Class', private_lessons: 'Private Lessons', behaviourist: 'Referred to Behaviourist' };
                return labels[o] || o;
            }
        }">
        @csrf

        @php
        $steps = [
            1 => [
                'title'    => 'Step 1 — Entry Observation',
                'subtitle' => 'Request dog to move to marker',
                'criteria' => [
                    1 => 'Calm entry, soft body, brief scanning',
                    2 => 'Mild pulling, alert but responsive',
                    3 => 'Panting, hypervigilance, frequent scanning',
                    4 => 'Tension on lead, vocalising, hesitation at threshold',
                    5 => 'Freezing, barking, tail tucked or high/stiff, unable to focus',
                ],
            ],
            2 => [
                'title'    => 'Step 2 — Comfort Level',
                'subtitle' => 'Handler offers dog a treat',
                'criteria' => [
                    1 => 'Eats treat calmly and promptly with soft body language',
                    2 => 'Takes treat with mild hesitation but remains engaged',
                    3 => 'Sniffs treat, delays eating, slightly tense posture',
                    4 => 'Takes treat slowly or reluctantly, frequently drops it',
                    5 => 'Refuses treat entirely, shows signs of distress',
                ],
            ],
            3 => [
                'title'    => 'Step 3 — Simple Cue Check',
                'subtitle' => 'Name, sit, etc.',
                'criteria' => [
                    1 => 'Responds quickly, takes food, remains engaged',
                    2 => 'Minor delay, easily redirected, responsive',
                    3 => 'Distracted, delayed response, intermittent food',
                    4 => 'Frequently disengaged, minimal food interest',
                    5 => 'Ignores cues, refuses food, unable to engage',
                ],
            ],
            4 => [
                'title'    => 'Step 4 — Neutral Stranger Approach',
                'subtitle' => 'Within 2m — contact if appropriate',
                'criteria' => [
                    1 => 'Calm, greets or ignores without tension',
                    2 => 'Shows interest or mild avoidance, no escalation',
                    3 => 'Lip licking, leaning on handler, hesitant',
                    4 => 'Backs away, freezes, avoids eye contact',
                    5 => 'Growling, barking, air snapping or stiff stillness',
                ],
            ],
            5 => [
                'title'    => 'Step 5 — Walking Past Dogs',
                'subtitle' => 'At least 10m',
                'criteria' => [
                    1 => 'Calmly observes, no fixation, soft body',
                    2 => 'Shows interest, orients to handler easily',
                    3 => 'Fixates briefly, minor pulling, takes food',
                    4 => 'Barking, pulling, slow recovery',
                    5 => 'Sustained reaction, unable to re-engage',
                ],
            ],
            6 => [
                'title'    => 'Step 6 — Low-Level Distraction',
                'subtitle' => 'Drop file nearby, shake jacket, etc.',
                'criteria' => [
                    1 => 'Startles and recovers instantly, investigates calmly',
                    2 => 'Startles slightly, reorients with support',
                    3 => 'Shows moderate concern, pauses but re-engages',
                    4 => 'Avoids or escalates, takes time to recover',
                    5 => 'Freezes or panics, unable to recover',
                ],
            ],
            7 => [
                'title'    => 'Step 7 — Handler Separation',
                'subtitle' => 'Instructor holds — handler walks 5 paces away & returns',
                'criteria' => [
                    1 => 'Stays relaxed, reorients calmly',
                    2 => 'Mild concern, checks surroundings then settles',
                    3 => 'Whines briefly, paces, settles with reassurance',
                    4 => 'Escalates: pacing, jumping, pulling to follow',
                    5 => 'Sustained distress, frantic or not attempted',
                ],
            ],
        ];
        @endphp

        {{-- Score key --}}
        <div class="card bg-navy/5 border border-navy/10 mb-6">
            <div class="flex items-center gap-6 flex-wrap text-sm">
                <span class="font-semibold text-navy text-xs uppercase tracking-wide">Score key:</span>
                <span><span class="font-bold text-navy">1</span> = Calm / ideal</span>
                <span><span class="font-bold text-navy">2</span> = Mild concern</span>
                <span><span class="font-bold text-navy">3</span> = Moderate</span>
                <span><span class="font-bold text-navy">4</span> = Significant concern</span>
                <span><span class="font-bold text-navy">5</span> = Severe / cannot continue</span>
            </div>
        </div>

        <div class="space-y-6">

            {{-- Steps --}}
            @foreach($steps as $stepNum => $step)
            <div class="card" x-show="{{ $stepNum === 7 ? '!step7Skipped' : 'true' }}">
                <div class="flex items-start justify-between gap-4 mb-1">
                    <div>
                        <h2 class="font-semibold text-navy">{{ $step['title'] }}</h2>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $step['subtitle'] }}</p>
                    </div>
                    @if($stepNum === 7)
                    <label class="flex items-center gap-2 text-sm flex-shrink-0 cursor-pointer">
                        <input type="checkbox" name="step7_skipped" value="1"
                            x-model="step7Skipped"
                            class="w-4 h-4 rounded border-gray-300 text-amber focus:ring-amber">
                        <span class="text-amber font-medium text-xs">Skip (aggression/distress)</span>
                    </label>
                    @endif
                </div>

                {{-- Criteria table — desktop --}}
                <div class="mb-4 text-xs text-gray-500 hidden sm:block mt-3">
                    <div class="grid grid-cols-5 gap-1">
                        @foreach($step['criteria'] as $score => $desc)
                        <div class="p-2 bg-gray-50 rounded-lg" :class="scores.step{{ $stepNum }} === {{ $score }} ? 'bg-navy/10 ring-1 ring-navy/30' : ''">
                            <div class="font-bold text-navy mb-1">{{ $score }}</div>
                            <div class="leading-snug">{{ $desc }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Score buttons --}}
                <div class="flex gap-2 mb-4">
                    @foreach([1, 2, 3, 4, 5] as $score)
                    <button type="button"
                        @click="scores.step{{ $stepNum }} = {{ $score }}"
                        :class="scores.step{{ $stepNum }} === {{ $score }}
                            ? 'bg-navy !text-white border-navy'
                            : 'bg-white text-gray-600 border-gray-300 hover:border-navy hover:text-navy'"
                        class="rating-btn">
                        {{ $score }}
                    </button>
                    @endforeach
                    <input type="hidden" name="step{{ $stepNum }}_score" :value="scores.step{{ $stepNum }}">
                </div>

                {{-- Assessor notes --}}
                <div>
                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1 block">Assessor notes</label>
                    <textarea name="step{{ $stepNum }}_notes" rows="2" class="form-textarea w-full text-sm"
                        placeholder="Observations for this step...">{{ old('step'.$stepNum.'_notes', $existingScore?->{'step'.$stepNum.'_notes'}) }}</textarea>
                </div>

                {{-- Mobile criteria --}}
                <div class="mt-2 sm:hidden text-xs text-gray-500 space-y-1">
                    @foreach($step['criteria'] as $score => $desc)
                    <div :class="scores.step{{ $stepNum }} === {{ $score }} ? 'text-navy font-semibold' : ''">
                        <span class="font-bold">{{ $score }}:</span> {{ $desc }}
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach

            {{-- Step 7 skip --}}
            <div class="card border-amber/30 bg-amber/5" x-show="step7Skipped" x-cloak>
                <h2 class="font-semibold text-navy mb-1">Step 7 — Skip Reason</h2>
                <p class="text-xs text-amber mb-3">*Only attempt if dog has NOT shown any aggression or severe distress</p>
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1 block">Describe behaviour observed</label>
                <textarea name="step7_skip_reason" rows="2" class="form-textarea w-full"
                    placeholder="Describe the behaviour that led to skipping Step 7...">{{ old('step7_skip_reason', $existingScore?->step7_skip_reason) }}</textarea>
                <input type="hidden" name="step7_skipped" :value="step7Skipped ? 1 : 0">
            </div>

            {{-- Outcome summary --}}
            <div class="card border-2" :class="{
                'border-green-300': recommendedOutcome === 'group_class',
                'border-amber':     recommendedOutcome === 'private_lessons',
                'border-red-400':   recommendedOutcome === 'behaviourist',
                'border-gray-200':  !recommendedOutcome
            }">
                <h2 class="font-semibold text-navy mb-4">Scoring Summary &amp; Recommendation</h2>

                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div class="text-center p-4 bg-gray-50 rounded-xl">
                        <p class="text-xs text-gray-500 mb-1">Average Score</p>
                        <p class="text-3xl font-bold text-navy" x-text="averageScore ?? '—'"></p>
                    </div>
                    <div class="col-span-2 p-4 rounded-xl" :class="{
                        'bg-green-50':  recommendedOutcome === 'group_class',
                        'bg-amber/10':  recommendedOutcome === 'private_lessons',
                        'bg-red-50':    recommendedOutcome === 'behaviourist',
                        'bg-gray-50':   !recommendedOutcome
                    }">
                        <p class="text-xs text-gray-500 mb-1">Recommended Outcome</p>
                        <p class="text-lg font-bold" x-text="recommendedOutcome ? outcomeLabel(recommendedOutcome) : '—'"
                            :class="{
                                'text-green-600': recommendedOutcome === 'group_class',
                                'text-amber':     recommendedOutcome === 'private_lessons',
                                'text-red-600':   recommendedOutcome === 'behaviourist',
                                'text-gray-400':  !recommendedOutcome
                            }">
                        </p>
                        <p class="text-xs text-gray-400 mt-1">Avg ≤ 2.5 → Group · Avg ≤ 3.5 → Private · Avg &gt; 3.5 → Behaviourist</p>
                    </div>
                </div>

                {{-- Override --}}
                <div class="space-y-3 mb-4">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="override_toggle" x-model="overrideOutcome"
                            class="w-4 h-4 rounded border-gray-300 text-brand focus:ring-brand">
                        <label for="override_toggle" class="text-sm font-medium text-gray-700">Override recommended outcome</label>
                    </div>
                    <div x-show="overrideOutcome" x-cloak class="space-y-3 pl-7">
                        <div>
                            <label class="form-label">Final Outcome</label>
                            <select name="final_outcome" class="form-select w-full">
                                <option value="">Use recommended (@{{ outcomeLabel(recommendedOutcome) }})</option>
                                <option value="group_class" @selected(old('final_outcome', $existingScore?->final_outcome) === 'group_class')>Group Class</option>
                                <option value="private_lessons" @selected(old('final_outcome', $existingScore?->final_outcome) === 'private_lessons')>Private Lessons</option>
                                <option value="behaviourist" @selected(old('final_outcome', $existingScore?->final_outcome) === 'behaviourist')>Referred to Behaviourist</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Override Reason <span class="text-red-500">*</span></label>
                            <textarea name="override_reason" rows="2" class="form-textarea w-full"
                                placeholder="Explain why the outcome was overridden...">{{ old('override_reason', $existingScore?->override_reason) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Global notes --}}
                <div>
                    <label class="form-label">Overall Evaluator Notes</label>
                    <textarea name="global_notes" rows="3" class="form-textarea w-full"
                        placeholder="Any overall observations, context, or recommendations for this dog and handler...">{{ old('global_notes', $existingScore?->global_notes) }}</textarea>
                </div>
            </div>

            {{-- Outcome email extras --}}
            <div class="card" x-show="recommendedOutcome === 'group_class' || (overrideOutcome)" x-cloak>
                <h2 class="font-semibold text-navy mb-1">Outcome Email — Class Types</h2>
                <p class="text-xs text-gray-400 mb-4">Select the class types to include in the outcome email. Each will link to its info page.</p>
                @if($availableClassTypes->count())
                @php $selectedIds = old('recommended_class_ids', $existingScore?->recommended_class_ids ?? []); @endphp
                <div class="space-y-2">
                    @foreach($availableClassTypes as $classType)
                    <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors">
                        <input type="checkbox" name="recommended_class_ids[]" value="{{ $classType->id }}"
                            class="w-4 h-4 rounded border-gray-300 text-brand focus:ring-brand"
                            @checked(in_array($classType->id, $selectedIds))>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-navy">{{ $classType->name }}</p>
                            @if($classType->tagline)
                            <p class="text-xs text-gray-400">{{ $classType->tagline }}</p>
                            @endif
                        </div>
                    </label>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-400">No class types with info pages are configured.</p>
                @endif
            </div>

            {{-- Send outcome email --}}
            <div class="card bg-brand/5 border border-brand/20">
                <div class="flex items-start gap-3">
                    <input type="checkbox" name="send_outcome_email" id="send_outcome_email" value="1"
                        class="w-4 h-4 mt-0.5 rounded border-gray-300 text-brand focus:ring-brand" checked>
                    <div>
                        <label for="send_outcome_email" class="text-sm font-medium text-navy cursor-pointer">Send outcome email to handler</label>
                        <p class="text-xs text-gray-400 mt-0.5">An email based on the outcome template will be sent to {{ $assessmentRequest->handler?->user?->email }}.</p>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex gap-3 pb-6">
                <button type="submit" class="btn-primary btn-lg">Submit Assessment Score</button>
                <a href="{{ route('admin.assessments.show', $assessmentRequest) }}" class="btn-outline btn-lg">Cancel</a>
            </div>

        </div>
    </form>

</div>
</x-app-layout>
