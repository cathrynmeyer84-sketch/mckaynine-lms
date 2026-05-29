<?php

namespace Database\Seeders;

use App\Models\ClassTypeWeek;
use App\Models\InstructorBriefingItem;
use Illuminate\Database\Seeder;

class PuppyBriefingSeeder extends Seeder
{
    /**
     * Seed instructor briefing items for Puppy Class weeks 2–6.
     * Week 1 is already populated. This seeder is safe to re-run —
     * it clears and rebuilds weeks 2–6 only.
     */
    public function run(): void
    {
        // class_type_id = 1 (Puppy Class)
        $weeks = ClassTypeWeek::where('class_type_id', 1)
            ->orderBy('week_number')
            ->get()
            ->keyBy('week_number');

        $data = [

            // ─────────────────────────────────────────────────────────────
            // WEEK 2
            // ─────────────────────────────────────────────────────────────
            2 => [
                [
                    'exercise_name' => 'Homework Feedback',
                    'suggested_time' => '5 minutes',
                    'sort_order' => 0,
                    'description' => "Open the floor briefly:
- How was the week?
- What did you practise and how did it go?
- Any concerns or questions?

Keep this moving — acknowledge each handler but don't let one person dominate. Note any common themes to address during theory.",
                ],
                [
                    'exercise_name' => 'Theory: Feeding & Toileting',
                    'suggested_time' => '8 minutes',
                    'sort_order' => 1,
                    'description' => "FEEDING
- Puppies under 6 months: feed 3× per day
- Quality puppy food — follow bag guidelines adjusted for training treats
- Keep to a schedule — helps predict toilet times

TOILETING
- Take pup outside immediately after waking, eating, and playing
- Pick a consistent toilet spot if possible
- Use a cue word: 'Go toilet' / 'Busy busy' — say it as they are going
- Reward immediately after — don't wait until they come back inside
- Accidents indoors: clean with enzyme cleaner, no punishment
- Unusual stools (blood, mucus, very loose) → vet check",
                ],
                [
                    'exercise_name' => 'Theory: Chewing',
                    'suggested_time' => '5 minutes',
                    'sort_order' => 2,
                    'description' => "Normal puppy behaviour — teething starts from 3–4 months, can last to 6–7 months.

MANAGEMENT FIRST
- Remove access to forbidden items (shoes off floor, cables hidden, rugs up)
- If you don't want it chewed, put it away

REDIRECTION
- Offer an appropriate alternative immediately: frozen Kong, bully stick, raw meaty bone (vet-approved), rubber toy
- Praise when pup takes the appropriate item

WHAT DOESN'T WORK
- Punishment after the fact (pup cannot connect it)
- Shouting — can make pup anxious and chew more
- 'Booby traps' — management is more reliable",
                ],
                [
                    'exercise_name' => 'Theory: Crate Introduction',
                    'suggested_time' => '5 minutes',
                    'sort_order' => 3,
                    'description' => "Goal: crate = pup's safe, happy place.

NEVER force the pup in or use as punishment.

STEPS
1. Leave crate open with bedding — let pup investigate freely
2. Drop high-value treats inside without closing door
3. Feed meals inside the crate (door open at first)
4. Once pup goes in voluntarily, gently close door for a few seconds, then open again — treat and praise
5. Build duration slowly over days/weeks
6. Cover with a blanket on 3 sides — more den-like
7. Ideal sizes: pup can stand, turn around, lie stretched out — not much bigger

NIGHT TIME
- Crate in bedroom initially so pup can hear you
- One toilet break overnight for young pups is normal",
                ],
                [
                    'exercise_name' => 'Pen Zen: Owner Walks Around Pen',
                    'suggested_time' => '5 minutes',
                    'sort_order' => 4,
                    'description' => "Progression from Week 1 (owner standing next to pen).

- Handlers start standing next to pen, then begin walking slowly around it
- Drop a treat into pen every 10–15 seconds to reward calmness
- Pup should remain settled — not barking or pawing at the fence
- If a pup is struggling, handler stays closer before increasing distance
- Goal: pup learns to be calm even when owner moves away",
                ],
                [
                    'exercise_name' => 'Settling: "Settle" + Scatter on Mat',
                    'suggested_time' => '5 minutes',
                    'sort_order' => 5,
                    'description' => "Progression from Week 1 (scatter only, no cue).

- Say \"Settle\" in a calm, low voice — one time only
- Then scatter treats on the mat
- No luring, no repeated cues
- Reward any offered calm behaviour — lying, reducing movement
- If pup is very excitable: ask handler to scatter treats before saying the cue word to build the association first",
                ],
                [
                    'exercise_name' => 'Exercise: Down',
                    'suggested_time' => '7 minutes',
                    'sort_order' => 6,
                    'description' => "LURING TECHNIQUE
1. Start with pup in a sit
2. Hold treat at nose, slowly bring treat straight down to the floor between front paws
3. As elbows touch the floor → mark (yes/click) and reward
4. Don't push the pup down — lure only

COMMON MISTAKES
- Luring too fast → pup stands instead of folding down
- Treating before elbows are fully on the floor
- Asking for sit first every time (some pups do better from a stand — try both)

ADDING THE CUE
- Only add \"Down\" once pup is reliably following the lure 8/10 times
- Say cue, lure, mark, reward → gradually fade the lure",
                ],
                [
                    'exercise_name' => 'Exercise: Front Touch — Front Feet on Platform',
                    'suggested_time' => '5 minutes',
                    'sort_order' => 7,
                    'description' => "USE: Low, stable platform (rubber mat on a step, wobble cushion base, etc.)

STEPS
1. Hold treat just behind the platform at nose height — lure pup to step front feet up
2. Mark the moment both front feet are on the platform
3. Reward in position (don't recall pup off immediately)
4. Build duration: wait for 2 seconds, then 3, then 5 before rewarding
5. Add verbal cue once consistent: \"Feet\" / \"Touch\"

PURPOSE: Builds body awareness, platform work is foundation for future exercises (sit stay on platform, down on platform).",
                ],
                [
                    'exercise_name' => 'Obstacles (15 min)',
                    'suggested_time' => '15 minutes',
                    'sort_order' => 8,
                    'description' => "Work through stations — each handler gets time on each obstacle.

CRATE — FRONT FEET IN
- Open door, hold treat inside at floor level
- Lure front feet in, mark, treat inside
- Build to all 4 feet (next week's goal)

SHORT TUNNEL
- Handler holds one end, assistant or helper crouches at other end calling pup
- Or: lure with treat through short, straight tunnel
- Keep it short and fun — no forcing

PLATFORM — SIT ON PLATFORM
- Lure all 4 feet onto platform, ask for sit
- Reward in position

RAMP
- Angle should be very gentle for first introduction
- Lure slowly up and back down
- Treat every few steps — build confidence
- Never rush — fearful pup at this stage creates a lasting problem

Increase complexity in small steps each week.",
                ],
                [
                    'exercise_name' => 'Bring From Home Next Week: Big Bag',
                    'suggested_time' => null,
                    'sort_order' => 9,
                    'description' => "Remind all handlers at end of class:

\"Next week, please bring a big bag from home — something like a large shopping bag, sports bag, or overnight bag. It should be something that makes noise when moved. We'll be using it for socialisation.\"",
                ],
            ],

            // ─────────────────────────────────────────────────────────────
            // WEEK 3
            // ─────────────────────────────────────────────────────────────
            3 => [
                [
                    'exercise_name' => 'Feedback & Homework Check',
                    'suggested_time' => '5 minutes',
                    'sort_order' => 0,
                    'description' => "Quick round:
- How is the Down coming along?
- Any crate progress?
- Chewing issues sorted?

Note any common struggles — address briefly now or weave into relevant theory below.",
                ],
                [
                    'exercise_name' => 'Theory: Retrieving & Digging',
                    'suggested_time' => '8 minutes',
                    'sort_order' => 1,
                    'description' => "RETRIEVING
- Retrieving instinct is strong in many breeds — harness it positively
- Teach a basic retrieve: throw toy, pup picks it up, recall pup back and swap for treat
- Don't chase after the pup — makes it a game to keep away
- Two-toy game: throw one toy, when pup returns with it show the second toy, they drop the first to get the second
- Tug is fine and healthy — teach a \"drop it\" / \"out\" cue first

DIGGING
- Often a breed trait or boredom behaviour
- Provide a designated dig spot (sandbox/soft patch) — bury toys and treats there
- Interrupt and redirect to the dig spot consistently
- More exercise, enrichment, and mental stimulation reduces problem digging",
                ],
                [
                    'exercise_name' => 'Theory: Aggression',
                    'suggested_time' => '8 minutes',
                    'sort_order' => 2,
                    'description' => "Important: keep tone factual and non-alarmist.

WHAT IS AGGRESSION?
- A normal communication tool that has been triggered or reinforced
- Common triggers: fear, pain, resource guarding, frustration, lack of socialisation

GROWLING IS INFORMATION — DO NOT PUNISH IT
- A growl is a warning. Punishing it suppresses the warning but not the emotion
- A pup that stops growling before biting is more dangerous, not safer

TYPES TO WATCH FOR IN CLASS
- Redirected: pup frustrated on leash → bites handler
- Resource guarding: over food, toys, owner
- Fear-based: happens when pup is overwhelmed

WHAT TO DO
- If you see sustained or repeated growling/snapping between pups: separate calmly
- Refer handlers with aggression concerns to a one-on-one consult — do not try to solve in class
- Never use punishment-based methods for aggression",
                ],
                [
                    'exercise_name' => 'Pen Zen: Owner Walks Away, Pauses',
                    'suggested_time' => '5 minutes',
                    'sort_order' => 3,
                    'description' => "Progression from Week 2 (walking around pen).

- Handlers walk away from pen a few metres, pause, and return
- Vary direction and distance — keep it unpredictable
- Continue dropping treats in pen periodically
- If a pup is barking or frantic: handler returns sooner, stays closer — don't let pup rehearse the anxiety",
                ],
                [
                    'exercise_name' => 'Settling: "Settle", Wait for Response, Jackpot',
                    'suggested_time' => '5 minutes',
                    'sort_order' => 4,
                    'description' => "Progression from Week 2 (cue + scatter).

1. Say \"Settle\" once — then wait
2. Observe: does pup reduce movement, lower head, or lie down?
3. If yes → jackpot reward (5–6 treats delivered one by one while pup stays calm)
4. If pup gets up → no reward, reset, try again
5. Build the expectation that calmness after the cue pays off very well",
                ],
                [
                    'exercise_name' => 'Exercise: Sit Stay',
                    'suggested_time' => '6 minutes',
                    'sort_order' => 5,
                    'description' => "BUILD THE 3 D's — Duration, Distance, Distraction — one at a time, never together.

DURATION FIRST
1. Ask for sit
2. Pause 1 second → mark and reward (while pup stays in sit)
3. Build to 3 seconds, then 5 seconds
4. Release cue: \"OK\" or \"Free\" before pup breaks

COMMON MISTAKES
- Saying \"Stay\" repeatedly as a mantra — one cue is enough
- Marking after pup has already broken the sit
- Moving away (distance) before duration is solid
- Don't add distance this week — just duration",
                ],
                [
                    'exercise_name' => 'Exercise: Rest Position',
                    'suggested_time' => '6 minutes',
                    'sort_order' => 6,
                    'description' => "Rest position = pup lying on one hip, fully relaxed (not in a sphinx down).

WHY: Important for veterinary checks, grooming, and general physical relaxation.

HOW
1. Get pup into a down
2. Hold treat to the side and slightly behind the hip — wait
3. Pup will shift weight to one side to follow the treat → hip rolls to floor
4. Mark the moment the hip makes contact
5. Reward in position
6. Alternate sides over time

Note: This is a new and trickier lure for some pups — be patient and keep sessions very short.",
                ],
                [
                    'exercise_name' => 'Obstacles (15 min)',
                    'suggested_time' => '15 minutes',
                    'sort_order' => 7,
                    'description' => "CRATE — ALL FEET IN
- Progress from last week: lure all 4 feet inside
- Mark when last foot is in, treat inside crate
- Don't close door yet — just build confidence going in fully

STRAIGHT LONG TUNNEL
- Use a straight, open tunnel (pup can see through)
- Handler lures from one end, assistant encourages from the other
- Gradually increase length as confidence builds

SIT STAY ON PLATFORM
- Lure all 4 feet onto platform, ask for sit
- Build 3–5 second duration before rewarding
- Handler steps back half a step — small distance addition only

HOW TO PRESENT AT VET
- Pup stands on a non-slip surface
- Handler holds lightly at chest and hindquarters
- Practice: gentle ear handling, look in mouth, touch each paw, run hands along body
- Treat generously throughout — keep it positive and calm",
                ],
                [
                    'exercise_name' => 'Bring From Home Next Week: Wheeled Item',
                    'suggested_time' => null,
                    'sort_order' => 8,
                    'description' => "Remind all handlers:

\"Next week, please bring something with wheels — a pram, skateboard, scooter, wheeled bag, or trolley. We'll be using it for socialisation to moving objects.\"",
                ],
            ],

            // ─────────────────────────────────────────────────────────────
            // WEEK 4
            // ─────────────────────────────────────────────────────────────
            4 => [
                [
                    'exercise_name' => 'Feedback',
                    'suggested_time' => '5 minutes',
                    'sort_order' => 0,
                    'description' => "Quick round:
- How is the sit stay duration building?
- Any success with rest position?
- How are pups doing with vet-style handling at home?

Acknowledge progress — week 4 is when handlers often feel the curve is steep.",
                ],
                [
                    'exercise_name' => 'Theory: Sterilisation & Car Travel',
                    'suggested_time' => '8 minutes',
                    'sort_order' => 1,
                    'description' => "STERILISATION
- Recommend handlers discuss timing with their vet
- General guidance: most vets recommend 6 months+, some large breeds benefit from waiting longer
- Benefits: reduces unwanted litters, reduces certain cancers, can reduce some roaming/marking behaviours
- Not a behaviour quick-fix — a settled dog before sterilisation is a settled dog after
- Recovery: restrict activity post-op, prevent pup licking wound, follow vet instructions

CAR TRAVEL
- Build a positive association early — short trips to good destinations, not only vet
- Car sickness: common in puppies — short trips, no feeding 2 hours before travel, good ventilation
- Restraint: harness with seatbelt clip, crate, or boot barrier — for safety of pup and occupants
- Anxious travellers: desensitise gradually — in stationary car with treats, then short drives",
                ],
                [
                    'exercise_name' => 'Theory: Grooming',
                    'suggested_time' => '7 minutes',
                    'sort_order' => 2,
                    'description' => "Build a lifelong positive association with grooming NOW — much easier than fixing resistance later.

BRUSHING
- Start with soft brush, short sessions (30 seconds)
- Treat every few strokes
- Gradually increase duration as pup relaxes

NAILS
- Touch paws frequently — don't wait until nail trim day
- Use nail file/dremel if pup is sensitive to clippers
- Trim a tiny amount, reward generously
- If pup is very resistant, a groomer or vet nurse can help

TEETH
- Introduce finger brush first, then toothbrush
- Use dog-safe toothpaste (never human — toxic)
- Daily brushing is ideal — even 30 seconds makes a difference

EARS
- Check weekly — floppy-eared breeds are more prone to infection
- Wipe outer ear with a damp cotton ball if dirty
- Never insert anything into the ear canal

BATHING
- Max once every 4–6 weeks to preserve natural oils
- Non-slip surface in the bath, warm (not hot) water
- Dry thoroughly — especially skin folds and ear canals",
                ],
                [
                    'exercise_name' => 'Socialisation: Wheelie Things (from home)',
                    'suggested_time' => '5 minutes',
                    'sort_order' => 3,
                    'description' => "Use the wheeled items handlers have brought from home.

APPROACH
- Let the wheeled item sit still first — allow pup to investigate at their own pace
- Reward sniffing and calm investigation heavily
- Gradually: have handler or assistant move the item slowly while pup watches
- If pup is relaxed, let it move past the pup
- Do NOT chase the pup with it or force proximity

WATCH FOR
- Pups that freeze, tuck tail, or refuse treats = overwhelmed → increase distance
- Pups that bark/lunge = redirect with food, don't let them rehearse the reaction",
                ],
                [
                    'exercise_name' => 'Settling: "Settle" → Lure into Down',
                    'suggested_time' => '5 minutes',
                    'sort_order' => 4,
                    'description' => "Progression: After the settle cue and response, lure pup into a down position on the mat.

1. Say \"Settle\"
2. Wait for pup to reduce movement / approach mat
3. Lure into down using treat from nose to floor
4. Mark and reward in the down
5. Build 5–10 second duration before releasing

Goal: settle cue starts to mean 'go to mat and lie down'.",
                ],
                [
                    'exercise_name' => 'Exercise: Take It',
                    'suggested_time' => '6 minutes',
                    'sort_order' => 5,
                    'description' => "Foundation for retrieve, toy play, and hand targeting.

TEACHING TAKE IT
1. Hold a toy or treat-wrapped object at pup's nose level
2. The moment pup puts mouth on it → mark and reward (treat from other hand)
3. Add cue: say \"Take it\" as you present the object
4. Build to pup holding briefly before marking

TEACHING \"LEAVE IT\" / \"DROP IT\" PAIRING
- Present item, pup takes it → immediately present a treat near nose
- Pup drops the item to get the treat → mark \"Drop it\" / \"Give\"
- Never reach into pup's mouth to remove — trade up always

Common mistake: clicking/marking too early before pup actually grasps the item.",
                ],
                [
                    'exercise_name' => 'Exercise: Supported Side Bends',
                    'suggested_time' => '6 minutes',
                    'sort_order' => 6,
                    'description' => "Physical conditioning exercise — promotes spinal flexibility and body awareness.

HOW
1. Pup standing or in a sit
2. Hold treat at pup's nose, arc it slowly back towards their hip/flank
3. Pup follows the treat, bending their neck and body sideways
4. Mark the bend, reward
5. Alternate sides — both sides equally important
6. Keep the movement slow and deliberate — this is not a quick flick

WHAT TO WATCH
- Pup should bend smoothly, not twist or jump
- If pup is stiff one side, note this and mention to handler — worth a vet check
- Keep sessions very short (3–4 reps per side)",
                ],
                [
                    'exercise_name' => 'Obstacles (15 min)',
                    'suggested_time' => '15 minutes',
                    'sort_order' => 7,
                    'description' => "CRATE — REINFORCE INSIDE
- Pup goes fully in, handler gently closes door for 5–10 seconds
- Treat through the door while inside
- Open before pup shows any stress
- Gradually build duration

BENT TUNNEL
- Tunnel is now bent so pup cannot see the exit
- Handler feeds into one end, assistant calls from the other
- If pup hesitates: shorten the bend so there's a small visual gap, then increase

DOWN ON PLATFORM
- All 4 feet on platform, ask for down
- Build 5-second duration
- Reward in position — pup should not jump off until released

CAVALETTIES
- Low poles/rails on the ground for pup to step over
- Walk pup slowly through — no rushing
- Builds proprioception and body awareness
- Treats every 2–3 poles to keep engagement",
                ],
                [
                    'exercise_name' => 'Bring From Home Next Week: Something Weird!',
                    'suggested_time' => null,
                    'sort_order' => 8,
                    'description' => "Remind all handlers:

\"Next week, bring something weird from home — the stranger the better! Think: a pool noodle, Halloween mask, umbrella, large stuffed animal, traffic cone, tarpaulin, anything unusual. We'll use it to practise socialisation to unexpected objects.\"",
                ],
            ],

            // ─────────────────────────────────────────────────────────────
            // WEEK 5
            // ─────────────────────────────────────────────────────────────
            5 => [
                [
                    'exercise_name' => 'Feedback',
                    'suggested_time' => '5 minutes',
                    'sort_order' => 0,
                    'description' => "Quick round — second-to-last lesson so check in on overall progress:
- How is the retrieve/take it going?
- Any grooming wins?
- Any behaviour concerns that have emerged at home?

Note: some handlers start worrying about what happens after class ends — this week and next are good times to address continuity.",
                ],
                [
                    'exercise_name' => 'Theory: First Aid',
                    'suggested_time' => '10 minutes',
                    'sort_order' => 1,
                    'description' => "WHEN TO CALL THE VET IMMEDIATELY
- Difficulty breathing or open-mouth breathing (dogs don't pant to breathe — this is a sign of distress)
- Collapse or sudden inability to stand
- Seizures
- Suspected poisoning (chocolate, xylitol, grapes/raisins, macadamia nuts, medications)
- Bloated/distended abdomen + retching without vomiting
- Pale/white gums
- Heavy bleeding that doesn't stop with pressure

BASIC HOME FIRST AID
- Minor cuts: clean with saline, cover with non-stick dressing, vet if deep or won't stop bleeding
- Bee sting: remove stinger, apply cold compress, watch for allergic reaction (swelling of face/throat = emergency)
- Burns: cool with running water 10–15 min, do not apply butter or toothpaste, vet always
- Choking: look in mouth only if safe to do so — if pup cannot breathe, to vet immediately

NEVER GIVE
- Human pain medication (paracetamol, ibuprofen are toxic to dogs)
- Aspirin without vet guidance

Recommend handlers save their vet's emergency number in their phone.",
                ],
                [
                    'exercise_name' => 'Theory: Object Swap',
                    'suggested_time' => '8 minutes',
                    'sort_order' => 2,
                    'description' => "Object swapping = trading something from the pup's mouth for something of equal or higher value.

WHY IT MATTERS
- Resource guarding is common and can escalate
- Repeated practice of swapping teaches pup that giving up an item predicts something good
- This prevents the pup running away with stolen items (the chase game)

HOW TO PRACTISE AT HOME
1. Offer a toy, let pup engage with it
2. Approach calmly (no grabbing or chase)
3. Present a high-value treat at nose level
4. Pup drops toy → immediately praise and give treat, then return the toy!
5. Returning the item teaches pup that giving it up doesn't mean losing it forever

WHAT NOT TO DO
- Don't grab, lunge, or make it a tug battle
- Don't swap down (toy → nothing)
- Never punish for picking up items — teach a reliable drop it instead

Socialisation: use the 'something weird' items brought from home — let pups investigate, reward curiosity.",
                ],
                [
                    'exercise_name' => 'Socialisation: Something Weird (from home)',
                    'suggested_time' => '5 minutes',
                    'sort_order' => 3,
                    'description' => "Use the unusual objects handlers have brought.

- Place items around the training area
- Allow pups to encounter them during movement exercises rather than forcing a direct approach
- Reward any voluntary investigation
- Handlers can also wear/carry unusual items (hat, mask, umbrella) while interacting with their own pup

Remember: the goal is a positive association, not just exposure. Seeing the weird thing is not enough — treats + calm handler body language makes the difference.",
                ],
                [
                    'exercise_name' => 'Settling: Reinforce Down on Mat',
                    'suggested_time' => '5 minutes',
                    'sort_order' => 4,
                    'description' => "Continuation of Weeks 3–4 progression.

- Cue \"Settle\" → expect pup to go to mat and offer a down
- Reward the down generously (jackpot)
- Build duration: 10–15 seconds in down before rewarding
- Handler can take a small step away while pup is in down

This is now becoming a reliable settle behaviour — handlers should be practising this at home daily.",
                ],
                [
                    'exercise_name' => 'Exercise: Puppy Pushups / IRS',
                    'suggested_time' => '6 minutes',
                    'sort_order' => 5,
                    'description' => "Puppy pushups = rapidly alternating sit → down → sit (builds fluency and focus).
IRS = Incompatible Reward Substitute (ask for a sit/down to replace unwanted behaviour).

PUPPY PUSHUPS
1. Ask for sit → mark + treat
2. Immediately ask for down → mark + treat
3. Ask for sit again → repeat
4. Speed this up as pup gets fluent — it becomes a fast, fun sequence
5. Only 4–5 reps per set — keep it snappy

IRS CONCEPT
- Explain to handlers: a dog that is sitting cannot also be jumping up
- When pup jumps on a visitor → ask for sit instead of saying \"no\" or pushing pup off
- The sit is the incompatible behaviour — reward the sit, ignore the jumping
- Practise: handlers greet each other's pups and ask for a sit before giving attention",
                ],
                [
                    'exercise_name' => 'Exercise: Injury Game',
                    'suggested_time' => '6 minutes',
                    'sort_order' => 6,
                    'description' => "Prepares pup for vet and emergency handling — desensitises to restraint and body handling.

GAME
1. Pup in a stand or down
2. Handler gently holds one paw — mark and treat immediately
3. Progress to: holding for 2 seconds, then 3 seconds
4. Rotate through all paws, then ears, mouth (open lips to look at teeth), tail
5. Simulate wrapping a paw (use a bandana or cloth loosely)

KEY PRINCIPLES
- Keep it fun and fast — short holds, high reward rate
- If pup pulls away: relax your hold (don't grip tighter), let pup settle, try again more gently
- This should feel like a game to the pup — watch body language

This exercise can prevent a lot of vet anxiety later in life.",
                ],
                [
                    'exercise_name' => 'Obstacles: Combination Work (15 min)',
                    'suggested_time' => '15 minutes',
                    'sort_order' => 7,
                    'description' => "Week 5 obstacles should now be combined into short sequences.

CRATE COMBO
- Cue pup into crate, close door 15–20 seconds, open and release

BENT TUBE + DISTRACTION
- Handler sends pup through bent tunnel while another handler walks past with their pup
- Or: toy/ball thrown near tunnel entrance as pup enters
- Goal: pup completes obstacle despite distraction

PLATFORM COMBOS
- Sit on platform → down on platform → release
- Or: recall onto platform, sit, down

CAVALETTIES
- Increase height slightly if ready
- Two obstacles in sequence

LONG RECALL
- Begin introducing a longer recall distance (5–8 metres)
- Handler crouches, high excitement, call once — huge jackpot reward when pup arrives",
                ],
                [
                    'exercise_name' => 'Bring From Home Next Week: Cookies for Teacher!',
                    'suggested_time' => null,
                    'sort_order' => 8,
                    'description' => "Last week of class!

\"It's our final lesson next week. Please bring cookies or a treat for the class to share — this is our little tradition! And come ready for some revision and celebration.\"",
                ],
            ],

            // ─────────────────────────────────────────────────────────────
            // WEEK 6
            // ─────────────────────────────────────────────────────────────
            6 => [
                [
                    'exercise_name' => 'Feedback & Final Check-In',
                    'suggested_time' => '5 minutes',
                    'sort_order' => 0,
                    'description' => "Last lesson — take a moment to celebrate progress.

- What's the biggest change you've seen in your pup over 6 weeks?
- What skill are you most proud of?
- What are you still working on?

Acknowledge each handler genuinely — this builds loyalty and referrals. Note handlers who might be good candidates for the next level / EO class.",
                ],
                [
                    'exercise_name' => 'Theory: The Future',
                    'suggested_time' => '10 minutes',
                    'sort_order' => 1,
                    'description' => "Cover what comes next and set handlers up for continued success.

ADOLESCENCE (4–18 months depending on breed)
- Pup will seem to \"forget\" everything — this is normal, not regression
- Keep training consistent — short sessions, stay positive
- Increase exercise and mental enrichment as energy levels rise
- Social behaviour can change — pups that were friendly may become selective

CONTINUED TRAINING
- Enrol in next level class (explain what the next class covers)
- Short, daily training sessions (5–10 min) are more effective than one long weekly session
- Teach new tricks — mental work tires a dog as much as physical exercise
- Socialisation never stops — keep exposing pup to new environments, people, and animals calmly

IMPORTANT REMINDERS
- Don't stop socialisation at 12 weeks — it continues until 18+ months
- A tired dog is a good dog — appropriate exercise prevents most problem behaviours
- The best investment in your dog's life is time spent training together

RECOMMENDED NEXT STEPS
- Name the next class type available and when it starts
- Mention any private lesson options for specific challenges
- Encourage handlers to stay connected via the app for resources and support",
                ],
                [
                    'exercise_name' => 'Settling: Full Review',
                    'suggested_time' => '5 minutes',
                    'sort_order' => 2,
                    'description' => "Final demonstration of the full settle progression:

1. Cue \"Settle\"
2. Pup goes to mat and offers a down
3. Handler steps away 1–2 metres
4. 15–30 second duration
5. Handler returns to pup and releases calmly (no fuss)

Highlight how far each pup has come from Week 1 (random treat scatter) to now (reliable cued settle with distance and duration).",
                ],
                [
                    'exercise_name' => 'Revision: Exercises',
                    'suggested_time' => '15 minutes',
                    'sort_order' => 3,
                    'description' => "Run through all exercises learnt in the course — let handlers show off!

SEQUENCE (can be done as a circuit or demo round):
1. Auto sit/focus — handler stops, pup sits without being asked
2. Down — from sit, lure fading
3. Sit stay — 5+ seconds, small distance
4. Take it / Drop it — demonstrate the trade
5. Puppy pushups — sit → down → sit, rapid sequence
6. Recall — from 4–5 metres, one call
7. Loose lead — short walk, no pulling
8. Rest position — down to hip settle
9. Supported side bends — one each side
10. Injury game — paw hold, ear check

Keep energy high — celebrate every attempt. This is their graduation demo.",
                ],
                [
                    'exercise_name' => 'Obstacles: Final Combinations & Long Recall',
                    'suggested_time' => '15 minutes',
                    'sort_order' => 4,
                    'description' => "FULL COMBO CIRCUIT
Set up a mini-course using all available obstacles in a flowing sequence. For example:
- Recall through tunnel → onto platform → down stay → release through cavaletties → into crate

LONG RECALL
- Maximum distance available in the space (8–10+ metres)
- One call, crouch, huge excitement, mega jackpot on arrival
- This is the showpiece — make it special

End with a round of applause for the pups and their handlers!",
                ],
                [
                    'exercise_name' => 'Graduation & Close',
                    'suggested_time' => '5 minutes',
                    'sort_order' => 5,
                    'description' => "Close the class warmly.

- Share any final thoughts or observations about each pup (keep it positive!)
- Remind handlers that their profile and resources remain accessible in the app
- Mention cookies and allow time for social interaction between handlers and pups
- Take a class photo if everyone is comfortable
- Congratulate everyone — completing puppy class is a real achievement!

\"You've given your puppy the best possible start. The work you put in now pays off for the next 10–15 years.\"",
                ],
            ],
        ];

        foreach ($data as $weekNumber => $items) {
            $week = $weeks->get($weekNumber);
            if (! $week) {
                $this->command->warn("Week {$weekNumber} not found for Puppy Class — skipping.");
                continue;
            }

            // Clear existing items for this week (safe to re-run)
            InstructorBriefingItem::where('class_type_week_id', $week->id)->delete();

            foreach ($items as $item) {
                InstructorBriefingItem::create(array_merge($item, [
                    'class_type_week_id' => $week->id,
                ]));
            }

            $this->command->info("Week {$weekNumber}: " . count($items) . " briefing items seeded.");
        }

        $this->command->info('Puppy class briefings seeded for weeks 2–6.');
    }
}
