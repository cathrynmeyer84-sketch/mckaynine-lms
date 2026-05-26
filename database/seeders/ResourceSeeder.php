<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Resource;

class ResourceSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = 1;

        $articles = [

            // ─────────────────────────────────────────────
            // GENERAL
            // ─────────────────────────────────────────────
            [
                'title'      => 'Training Ground Rules',
                'category'   => 'General',
                'sort_order' => 10,
                'content'    => <<<'HTML'
<h2>Training Ground Rules</h2>
<p>To keep our training environment safe and enjoyable for every dog and handler, please familiarise yourself with the following rules before your first class.</p>

<h3>Parking</h3>
<ul>
  <li>Please park in the designated areas and do not block the driveway or gate.</li>
  <li>Keep your dog inside your car until it is time for class to begin.</li>
</ul>

<h3>Leads & Collars</h3>
<ul>
  <li>All dogs must be on a lead at all times on the property — no exceptions.</li>
  <li>Use a plain, flat buckle collar or a well-fitted harness. Slip leads, choke chains, and prong collars are not permitted.</li>
  <li>Your lead should be approximately 1.5 m long. Retractable leads are not suitable for training classes.</li>
</ul>

<h3>Equipment</h3>
<ul>
  <li>Bring small, high-value treats (small pieces of cooked chicken, cheese, or liver cake work well).</li>
  <li>Bring a treat pouch or small bag to keep rewards easily accessible.</li>
  <li>Water and a bowl are recommended, especially in warm weather.</li>
</ul>

<h3>General Rules</h3>
<ul>
  <li>Arrive a few minutes before class so your dog can settle before work begins.</li>
  <li>If your dog is unwell, in season, or showing signs of aggression, please keep them home and contact your handler to let them know.</li>
  <li>Children are welcome but must remain with a responsible adult and should not approach other dogs without permission.</li>
  <li>Please silence your phone during class.</li>
  <li>No smoking on the training grounds.</li>
</ul>

<h3>Dog Interaction</h3>
<ul>
  <li>Do not allow your dog to greet other dogs without first asking the other handler's permission.</li>
  <li>Even friendly dogs can overwhelm a nervous dog. Keep greetings brief and calm.</li>
  <li>If your dog shows fear or reactivity towards other dogs, let your handler know so adjustments can be made.</li>
</ul>
HTML,
            ],

            [
                'title'      => 'Liver Cake Recipe',
                'category'   => 'General',
                'sort_order' => 20,
                'content'    => <<<'HTML'
<h2>Liver Cake Recipe</h2>
<p>Liver cake is one of the highest-value treats you can use in training. The smell alone is incredibly motivating for most dogs, making it perfect for teaching new behaviours or working in distracting environments.</p>

<h3>Ingredients</h3>
<ul>
  <li>500 g chicken livers (or ox liver)</li>
  <li>2 eggs</li>
  <li>250 g self-raising flour (or plain flour + 1 tsp baking powder)</li>
  <li>1 clove garlic (optional — many dogs love the smell)</li>
</ul>

<h3>Method</h3>
<ol>
  <li>Blend the liver in a food processor until smooth. Add the eggs and garlic and blend again.</li>
  <li>Pour into a bowl and stir in the flour until a thick batter forms.</li>
  <li>Pour into a greased baking tray (the mixture should be about 1–2 cm deep).</li>
  <li>Bake at 180 °C for 20–25 minutes until firm and set through.</li>
  <li>Allow to cool completely, then cut into small pea-sized cubes.</li>
</ol>

<h3>Storage</h3>
<ul>
  <li>Store in an airtight container in the fridge for up to one week.</li>
  <li>Liver cake freezes very well. Portion into zip-lock bags and freeze for up to three months.</li>
  <li>Defrost overnight in the fridge before use.</li>
</ul>

<p><strong>Tip:</strong> Keep pieces small — treats should be tiny rewards, not meals. You want your dog eager for the next one!</p>
HTML,
            ],

            [
                'title'      => 'Courses Available at McKaynine',
                'category'   => 'General',
                'sort_order' => 30,
                'content'    => <<<'HTML'
<h2>Courses Available at McKaynine</h2>
<p>McKaynine offers a progressive pathway of training courses designed to take you and your dog from the very beginning through to a high level of obedience and good citizenship.</p>

<h3>Puppy School</h3>
<p>For puppies aged 8–16 weeks (up to their first adult teeth). This foundational class covers socialisation, basic manners, bite inhibition, and the building blocks of a positive human–dog relationship. Classes are small and fun, with a focus on confidence-building for both puppy and owner.</p>

<h3>Elementary Obedience</h3>
<p>The next step after Puppy School (or suitable for older dogs starting training for the first time). Handlers learn core commands — sit, down, stay, recall, heel — using positive reinforcement. Dogs are graded at the end of the course and receive a certificate on successful completion.</p>

<h3>Canine Good Citizen (CGC)</h3>
<p>An internationally recognised programme assessing real-world manners and reliability. Dogs that achieve CGC have demonstrated that they are well-socialised, controllable, and a pleasure to own. This is the goal of the McKaynine training pathway.</p>

<h3>The Recommended Pathway</h3>
<p>Puppy School → Elementary Obedience → Canine Good Citizen</p>
<p>Each course builds on the last. Consistent attendance and daily home practice are the keys to success.</p>

<h3>Private Lessons</h3>
<p>One-on-one sessions are available for dogs with specific behavioural challenges, or for handlers who prefer personalised coaching. Contact McKaynine to discuss your needs.</p>
HTML,
            ],

            // ─────────────────────────────────────────────
            // ABOUT YOUR COURSE
            // ─────────────────────────────────────────────
            [
                'title'      => 'Why Is Puppy School So Important?',
                'category'   => 'About Your Course',
                'sort_order' => 40,
                'content'    => <<<'HTML'
<h2>Why Is Puppy School So Important?</h2>
<p>The single most important thing you can do for your puppy is to enrol in a reputable puppy socialisation class before 16 weeks of age. Here's why:</p>

<h3>The Critical Socialisation Window</h3>
<p>Puppies have a critical period of social development that closes at approximately 12–16 weeks of age. During this window, the brain is highly plastic and experiences shape the puppy's perception of the world for life. Positive exposures during this period build a confident, well-adjusted adult dog. Missed exposures — or negative experiences — can lead to fear and anxiety that is difficult to reverse later.</p>

<h3>What Puppy School Provides</h3>
<ul>
  <li><strong>Safe socialisation</strong> with other vaccinated puppies and people in a controlled environment.</li>
  <li><strong>Bite inhibition</strong> — learning to control the force of their mouth through play with other puppies.</li>
  <li><strong>Basic manners</strong> that make life with a dog easier from day one.</li>
  <li><strong>Owner education</strong> — understanding how dogs learn, and how to set your puppy up for success.</li>
  <li><strong>Early problem-solving</strong> — addressing issues like nipping, jumping, and toileting before they become habits.</li>
</ul>

<h3>The Bottom Line</h3>
<p>A well-socialised puppy is easier to train, less likely to develop behavioural problems, and is safer around people and other animals. Puppy school is not a luxury — it is an investment in your dog's entire life.</p>
HTML,
            ],

            [
                'title'      => 'Getting the Most from Your Classes',
                'category'   => 'About Your Course',
                'sort_order' => 50,
                'content'    => <<<'HTML'
<h2>Getting the Most from Your Classes</h2>
<p>Classes are just the beginning. What you do between sessions is what really determines how quickly your dog learns.</p>

<h3>Treat Your Lesson Notes as a Study Guide</h3>
<p>After each class, review your lesson notes. The exercises you have been taught are the homework for the week. Practise each one daily — even five to ten minutes a session is more effective than one long session once a week.</p>

<h3>Tips for Productive Practice</h3>
<ul>
  <li><strong>Keep sessions short</strong> — 5–10 minutes, two to three times a day. Dogs, especially puppies, have short attention spans.</li>
  <li><strong>End on a positive note</strong> — always finish with something your dog does well so they feel successful.</li>
  <li><strong>Reduce distractions at first</strong> — practise in a quiet environment before moving to more challenging settings.</li>
  <li><strong>Use high-value rewards</strong> — match the difficulty of the task to the quality of the treat.</li>
  <li><strong>Be consistent</strong> — use the same cue words and hand signals that were taught in class.</li>
</ul>

<h3>Ask Questions</h3>
<p>Your handler is there to help. If something isn't working at home, bring it up at the next class. There is no such thing as a silly question when it comes to your dog's training.</p>

<h3>The Golden Rule</h3>
<p>If you're getting frustrated, stop the session. Dogs read our emotions clearly, and frustration poisons the learning environment. Take a break, have a cup of tea, and come back when you're calm.</p>
HTML,
            ],

            [
                'title'      => 'Class Pointers — What to Expect Each Week',
                'category'   => 'About Your Course',
                'sort_order' => 60,
                'content'    => <<<'HTML'
<h2>Class Pointers — What to Expect Each Week</h2>

<h3>Puppy Class</h3>
<ul>
  <li>Classes begin with free socialisation time — puppies interact under supervision while handlers chat and settle in.</li>
  <li>Structured exercises are introduced progressively each week, building on what was learned before.</li>
  <li>Puppies learn best through play, so expect plenty of it!</li>
  <li>Each class ends with a brief recap and homework instructions.</li>
</ul>

<h3>Obedience Class</h3>
<ul>
  <li>Dogs must be on lead at all times unless instructed otherwise.</li>
  <li>Classes typically begin with a warm-up exercise to settle dogs and get handlers focused.</li>
  <li>New exercises are introduced, and previously taught exercises are refined.</li>
  <li>Your handler will circulate and give individual feedback.</li>
  <li>Expect to work on: heel, sit, down, stay, recall, and attention work.</li>
</ul>

<h3>General Advice</h3>
<ul>
  <li>Arrive on time — late arrivals can disrupt the class and unsettle other dogs.</li>
  <li>Don't feed your dog a large meal immediately before class — a slightly empty tummy makes for a more motivated worker.</li>
  <li>Wear comfortable shoes and clothes you don't mind getting dog hair or paw prints on!</li>
</ul>
HTML,
            ],

            [
                'title'      => 'Class Goals & Grading — Elementary Obedience',
                'category'   => 'About Your Course',
                'sort_order' => 70,
                'content'    => <<<'HTML'
<h2>Class Goals & Grading — Elementary Obedience</h2>
<p>At the end of the Elementary Obedience course, dogs are assessed to determine whether they have reached a satisfactory standard. This is a positive experience — a celebration of what you and your dog have achieved together.</p>

<h3>What Is Assessed</h3>
<ul>
  <li><strong>Heel on lead</strong> — walking without pulling, at handler's pace</li>
  <li><strong>Sit</strong> — on cue, promptly</li>
  <li><strong>Down</strong> — on cue, promptly</li>
  <li><strong>Stay</strong> — sit-stay and down-stay while handler moves away</li>
  <li><strong>Recall</strong> — coming when called reliably</li>
  <li><strong>Attention</strong> — ability to focus on the handler in a mildly distracting environment</li>
</ul>

<h3>Grading</h3>
<p>Dogs are graded on their performance across these exercises. The grading is designed to be encouraging, recognising progress made throughout the course rather than perfection.</p>

<h3>Certificates</h3>
<p>Dogs that meet the standard receive a McKaynine Elementary Obedience certificate. Those who need more time on certain exercises are welcome to repeat or continue into additional classes.</p>

<h3>Remember</h3>
<p>Every dog progresses at their own pace. What matters most is that you and your dog enjoy the process and continue to develop your relationship. Training is a lifelong journey — graduation is just the beginning.</p>
HTML,
            ],

            [
                'title'      => 'Homework — Practising Between Classes',
                'category'   => 'About Your Course',
                'sort_order' => 80,
                'content'    => <<<'HTML'
<h2>Homework — Practising Between Classes</h2>
<p>The dogs that progress fastest are those whose owners practise consistently between sessions. Class time alone is not enough — it is the daily repetitions at home that build reliable behaviour.</p>

<h3>How to Structure Home Practice</h3>
<ul>
  <li><strong>2–3 short sessions per day</strong> of 5–10 minutes each are far more effective than one long session.</li>
  <li>Practise in different locations as your dog improves — kitchen, garden, on walks, at the park.</li>
  <li>Gradually add distractions as your dog becomes reliable.</li>
</ul>

<h3>Using Meal Times</h3>
<p>You don't always need formal training sessions. Use your dog's meal kibble as reward treats and ask for a few sits, downs, or focus exercises before placing the bowl down. This integrates training into everyday life.</p>

<h3>Keeping a Training Log</h3>
<p>Some handlers find it helpful to keep a simple log: date, exercises practised, how many repetitions, and how the dog performed. This can reveal patterns — for example, that your dog struggles with stays in the garden but not indoors — and help you focus your efforts.</p>

<h3>What to Do If Your Dog Is Struggling</h3>
<p>Go back one step. Training always moves at the dog's pace. If your dog is failing more than they're succeeding, the exercise is too hard for where they are right now. Break it into smaller pieces and build back up.</p>
HTML,
            ],

            // ─────────────────────────────────────────────
            // OUR METHODS
            // ─────────────────────────────────────────────
            [
                'title'      => 'Positive Reinforcement — Why We Use It',
                'category'   => 'Our Methods',
                'sort_order' => 90,
                'content'    => <<<'HTML'
<h2>Positive Reinforcement — Why We Use It</h2>
<p>At McKaynine, all training is based on positive reinforcement. This means we reward the behaviours we want, and we set dogs up to succeed rather than waiting for them to fail.</p>

<h3>What Is Positive Reinforcement?</h3>
<p>Positive reinforcement means adding something the dog values (a treat, praise, play, or a toy) immediately after a desired behaviour, which makes that behaviour more likely to happen again. It is one of the most well-researched and effective training methods in animal learning science.</p>

<h3>Why Not Use Punishment?</h3>
<p>Punishment-based methods (corrections, choke chains, shock collars) may suppress behaviour in the short term, but they come with significant risks:</p>
<ul>
  <li>They can create fear, anxiety, and stress in the dog.</li>
  <li>They damage the trust between dog and owner.</li>
  <li>They can trigger defensive aggression.</li>
  <li>They teach the dog what <em>not</em> to do, but do not teach what <em to do instead.</li>
</ul>

<h3>The Benefits of Positive Training</h3>
<ul>
  <li>Dogs trained with positive reinforcement are more willing, enthusiastic, and engaged.</li>
  <li>It builds a strong bond and communication between handler and dog.</li>
  <li>It is safe — there is no risk of physical or psychological harm.</li>
  <li>It works on all dogs, regardless of size, breed, or age.</li>
  <li>It is enjoyable for both dog and human.</li>
</ul>

<h3>The Science Behind It</h3>
<p>Positive reinforcement is rooted in classical and operant conditioning — the same learning principles that apply to all mammals, including humans. When a behaviour produces a good outcome, the brain reinforces the neural pathway associated with that behaviour. It's not a trend — it's neuroscience.</p>
HTML,
            ],

            [
                'title'      => 'Behaviour Sums — Understanding How Your Dog Learns',
                'category'   => 'Our Methods',
                'sort_order' => 100,
                'content'    => <<<'HTML'
<h2>Behaviour Sums — Understanding How Your Dog Learns</h2>
<p>A useful way to understand dog training is to think of every behaviour as a simple "sum": the behaviour produces a consequence, and the consequence determines whether the behaviour increases or decreases in frequency.</p>

<h3>The Four Quadrants of Operant Conditioning</h3>
<table>
  <thead>
    <tr><th>Quadrant</th><th>What Happens</th><th>Effect on Behaviour</th></tr>
  </thead>
  <tbody>
    <tr><td><strong>Positive Reinforcement (R+)</strong></td><td>Add something good</td><td>Behaviour increases</td></tr>
    <tr><td><strong>Negative Reinforcement (R–)</strong></td><td>Remove something unpleasant</td><td>Behaviour increases</td></tr>
    <tr><td><strong>Positive Punishment (P+)</strong></td><td>Add something unpleasant</td><td>Behaviour decreases</td></tr>
    <tr><td><strong>Negative Punishment (P–)</strong></td><td>Remove something good</td><td>Behaviour decreases</td></tr>
  </tbody>
</table>

<h3>The Behaviour Sum in Practice</h3>
<p>Think of it this way: <strong>Behaviour + Consequence = Future Frequency</strong></p>
<ul>
  <li>Dog sits → receives a treat → sits more often. <em>(R+)</em></li>
  <li>Dog jumps up → owner turns away and ignores → jumping decreases. <em>(P–)</em></li>
  <li>Dog pulls on lead → walk stops → pulling decreases. <em>(P–)</em></li>
</ul>

<h3>Why This Matters</h3>
<p>Once you understand that every interaction with your dog is either reinforcing or discouraging a behaviour, you start to see your dog's behaviour differently. If an unwanted behaviour persists, ask: "What is my dog getting out of this?" Then remove that reward and reinforce an alternative behaviour instead.</p>

<p>Dogs are always learning. The question is: are we being intentional about what we're teaching?</p>
HTML,
            ],

            // ─────────────────────────────────────────────
            // ABOUT YOUR PUPPY
            // ─────────────────────────────────────────────
            [
                'title'      => 'Stages of Canine Development',
                'category'   => 'About Your Puppy',
                'sort_order' => 110,
                'content'    => <<<'HTML'
<h2>Stages of Canine Development</h2>
<p>Understanding the developmental stages your puppy goes through helps you make sense of their behaviour and respond appropriately at each stage.</p>

<h3>Neonatal Period (0–2 Weeks)</h3>
<p>Puppies are born blind and deaf. They are entirely dependent on their mother for warmth, nourishment, and stimulation. The primary senses at this stage are touch and smell.</p>

<h3>Transitional Period (2–3 Weeks)</h3>
<p>Eyes and ears open. The puppy begins to become aware of its surroundings and starts to interact with littermates.</p>

<h3>Socialisation Period (3–12 Weeks)</h3>
<p>This is the most critical developmental window. Between 3 and 12 weeks, puppies are primed to form positive associations with people, other animals, sounds, surfaces, and environments. Experiences during this period have a disproportionately large impact on adult behaviour. This is why puppy school before 16 weeks is so important.</p>
<ul>
  <li><strong>3–5 weeks:</strong> Primary socialisation — interaction with mother and littermates teaches bite inhibition and canine communication.</li>
  <li><strong>5–12 weeks:</strong> Human socialisation — the puppy should meet as many different types of people as possible.</li>
</ul>

<h3>Fear Imprint Period (8–10 Weeks)</h3>
<p>There is a brief fear imprint period within the socialisation window. Frightening experiences during this time can have lasting effects. Avoid traumatic events and be especially gentle and reassuring.</p>

<h3>Juvenile Period (3–6 Months)</h3>
<p>The puppy begins to test boundaries. Teething begins and chewing increases. Training becomes increasingly important during this phase.</p>

<h3>Adolescence (6–18 Months)</h3>
<p>Sexual maturity, increased independence, and what feels like "forgetting" everything they were taught. This is the most common age for dogs to be surrendered to shelters — don't give up! Consistent training through this phase produces the reliable adult dog you've been working towards.</p>

<h3>Social Maturity (18 Months – 3 Years)</h3>
<p>Dogs settle into their adult personality. Training gains during this period are very stable and long-lasting.</p>
HTML,
            ],

            [
                'title'      => 'Choosing the Right Breed for Your Lifestyle',
                'category'   => 'About Your Puppy',
                'sort_order' => 120,
                'content'    => <<<'HTML'
<h2>Choosing the Right Breed for Your Lifestyle</h2>
<p>One of the most important decisions you can make as a dog owner happens <em>before</em> you bring a dog home: choosing a breed (or type) that genuinely suits your lifestyle, home, and family.</p>

<h3>Energy Levels</h3>
<p>Different breeds have vastly different exercise and mental stimulation needs. A Border Collie or German Shepherd needs hours of daily activity and mental challenges. A Cavalier King Charles Spaniel or Basset Hound may be content with moderate walks. Matching the dog's energy to yours is fundamental.</p>

<h3>Size & Space</h3>
<p>Large breed dogs can live happily in smaller homes if adequately exercised, but some breeds genuinely do better with space. Consider your living situation honestly.</p>

<h3>Coat Care</h3>
<p>Long or double-coated breeds require regular grooming. Some breeds (Poodles, Bichons) need professional grooming every 6–8 weeks. Factor in time and cost.</p>

<h3>Breed Purpose</h3>
<p>Dogs were bred for specific tasks — herding, hunting, guarding, companionship. These drives don't disappear in the home. A herding dog will herd children. A terrier will dig. Understanding your breed's original purpose helps you channel their instincts productively.</p>

<h3>Children & Other Pets</h3>
<p>Research your chosen breed's general temperament with children and other animals. Some breeds are naturally more gentle or tolerant; others have high prey drives or guarding instincts that require careful management.</p>

<h3>Rescue vs. Breeder</h3>
<p>Rescue dogs make wonderful companions. Many are already housetrained and past the destructive puppy phase. If going to a breeder, always choose one who health-tests their breeding stock and prioritises temperament.</p>
HTML,
            ],

            // ─────────────────────────────────────────────
            // PUPPY & DOG PROBLEMS
            // ─────────────────────────────────────────────
            [
                'title'      => 'Toilet Training Your Puppy',
                'category'   => 'Puppy & Dog Problems',
                'sort_order' => 130,
                'content'    => <<<'HTML'
<h2>Toilet Training Your Puppy</h2>
<p>Toilet training requires patience, consistency, and a willingness to supervise closely. Most puppies can be reliably housetrained within a few weeks if the process is approached correctly.</p>

<h3>The Golden Rules</h3>
<ul>
  <li><strong>Supervise constantly</strong> when your puppy is indoors. If you can't watch, confine your puppy to a safe area.</li>
  <li><strong>Take them out frequently</strong> — after every meal, every nap, after play, and first thing in the morning and last thing at night.</li>
  <li><strong>Reward immediately</strong> when they toilet outside. Praise and a treat the moment they finish — not when they come back inside.</li>
  <li><strong>Go outside with them</strong> — don't just put them out. Watch so you can reward at the right moment.</li>
</ul>

<h3>Dealing with Accidents</h3>
<ul>
  <li>Do not punish accidents. The puppy cannot connect the punishment to the act (especially if you find it later). Punishment only teaches the puppy to hide toileting from you.</li>
  <li>Clean accidents thoroughly with an enzymatic cleaner to remove the smell. If it smells like a toilet to the puppy, it will be used as one.</li>
  <li>If you catch your puppy mid-act indoors, calmly and quickly take them outside to finish — then reward.</li>
</ul>

<h3>Crates as a Toilet Training Tool</h3>
<p>Dogs instinctively avoid soiling their sleeping area. A correctly sized crate (just big enough to stand, turn around, and lie down) can help teach bladder control when unsupervised. See the <em>Crate Training</em> article for more detail.</p>

<h3>Night Times</h3>
<p>Young puppies (under 12 weeks) may need a night-time toilet trip. Take them out quietly and without fuss — keep it boring so there's no reward for waking you up beyond the toilet itself.</p>

<h3>Progress</h3>
<p>Expect accidents for the first few weeks. Full reliability usually comes at 4–6 months as the puppy's bladder matures. Stick with it — it gets easier.</p>
HTML,
            ],

            [
                'title'      => 'Chewing — Managing & Redirecting',
                'category'   => 'Puppy & Dog Problems',
                'sort_order' => 140,
                'content'    => <<<'HTML'
<h2>Chewing — Managing & Redirecting</h2>
<p>Chewing is a completely normal and necessary behaviour for puppies. They chew to explore, to relieve teething pain, and because it is self-rewarding. Your job is not to stop chewing but to direct it onto appropriate items.</p>

<h3>Puppy-Proof Your Home</h3>
<ul>
  <li>Remove or secure anything you don't want destroyed — cables, shoes, furniture, children's toys.</li>
  <li>Prevention is far easier than correction.</li>
  <li>Use baby gates or a playpen to limit access to areas you can't supervise.</li>
</ul>

<h3>Provide Appropriate Chew Outlets</h3>
<ul>
  <li>Offer a variety of safe chew toys — rubber Kongs (stuffed with food), bully sticks, raw bones (under supervision), rope toys.</li>
  <li>Rotate toys to keep them interesting.</li>
  <li>A frozen stuffed Kong is an excellent, long-lasting chew option for a teething puppy.</li>
</ul>

<h3>Redirecting Unwanted Chewing</h3>
<p>If you catch your puppy chewing something inappropriate, calmly interrupt them (don't shout) and immediately offer an appropriate chew item. When they take it, praise them warmly. You're teaching: "chew <em>this</em>, not <em>that</em>."</p>

<h3>Things to Avoid</h3>
<ul>
  <li>Do not give old shoes or socks as chew toys — puppies cannot distinguish between "allowed" shoes and your best pair.</li>
  <li>Do not use punishment — it does not teach the puppy what to chew instead.</li>
  <li>Cooked bones can splinter and cause serious injury. Always use raw bones.</li>
</ul>

<h3>Adolescent Chewing</h3>
<p>Expect another increase in chewing around 6–8 months when adult teeth are coming in. Maintain good management and plenty of chew options through this phase.</p>
HTML,
            ],

            [
                'title'      => 'Biting & Bite Inhibition',
                'category'   => 'Puppy & Dog Problems',
                'sort_order' => 150,
                'content'    => <<<'HTML'
<h2>Biting & Bite Inhibition</h2>
<p>Puppy biting (also called "mouthing" or "nipping") is normal puppy behaviour. Puppies explore the world with their mouths and use play biting as a primary form of interaction. The goal is not to eliminate mouthing entirely, but to teach <strong>bite inhibition</strong> — a soft mouth — before the puppy's adult teeth come in.</p>

<h3>What Is Bite Inhibition?</h3>
<p>Bite inhibition is a dog's learned ability to control the pressure of their bite. A dog with good bite inhibition that is startled, hurt, or frightened may mouth a person but cause little or no damage. This is a critical safety skill for every dog.</p>

<h3>How Puppies Learn It Naturally</h3>
<p>In the litter, puppies learn bite inhibition from each other. When one puppy bites too hard, the other yelps and stops playing. The biting puppy learns that hard bites end the fun. We can replicate this at home.</p>

<h3>Teaching Bite Inhibition</h3>
<ol>
  <li>When your puppy bites too hard, make a short, sharp yelp ("ow!") and immediately withdraw all attention for 30–60 seconds.</li>
  <li>Resume play. If the puppy bites hard again, repeat.</li>
  <li>Over time, the puppy learns that hard biting ends playtime.</li>
  <li>Gradually reduce the acceptable pressure until the puppy is consistently gentle.</li>
</ol>

<h3>Additional Tips</h3>
<ul>
  <li>Never use your hands as play toys — always use a rope toy or tug.</li>
  <li>Ensure your puppy has adequate outlets for energy — a tired puppy bites less.</li>
  <li>Children should be taught how to respond to puppy biting and should be supervised with the puppy at all times.</li>
  <li>If biting is intense, frequent, or accompanies growling, consult your handler.</li>
</ul>
HTML,
            ],

            [
                'title'      => 'Barking — Understanding and Managing It',
                'category'   => 'Puppy & Dog Problems',
                'sort_order' => 160,
                'content'    => <<<'HTML'
<h2>Barking — Understanding and Managing It</h2>
<p>Barking is a dog's primary form of vocal communication. All dogs bark — the goal is to manage excessive barking rather than to silence your dog entirely.</p>

<h3>Why Do Dogs Bark?</h3>
<ul>
  <li><strong>Alert barking</strong> — notifying the household of an intruder, visitor, or unusual sound.</li>
  <li><strong>Territorial barking</strong> — guarding their space against perceived threats.</li>
  <li><strong>Demand barking</strong> — asking for attention, food, or play.</li>
  <li><strong>Fear or anxiety barking</strong> — in response to something frightening.</li>
  <li><strong>Boredom barking</strong> — under-stimulated dogs bark to entertain themselves.</li>
  <li><strong>Separation anxiety</strong> — distress barking when left alone.</li>
</ul>

<h3>Management Strategies</h3>
<p><strong>Alert barking:</strong> Acknowledge the bark ("thank you"), then use a calm "enough" cue and reward silence. Shutting curtains or blocking fence-line views can reduce visual triggers.</p>

<p><strong>Demand barking:</strong> Never reward demand barking with what the dog wants. Turn away and only give attention when the dog is quiet. This requires consistency from everyone in the household.</p>

<p><strong>Boredom barking:</strong> Increase physical exercise and mental stimulation. Puzzle feeders, training sessions, chew toys, and more frequent walks all help.</p>

<p><strong>Fear/Anxiety barking:</strong> Do not punish. Work on desensitisation and counter-conditioning. See your handler if this is a significant issue.</p>

<h3>What Not to Do</h3>
<ul>
  <li>Do not shout at a barking dog — they may interpret this as you joining in.</li>
  <li>Do not use punishment devices (citronella sprays, shock collars) — these address the symptom, not the cause, and can increase anxiety.</li>
</ul>
HTML,
            ],

            [
                'title'      => 'Digging — Why It Happens & What to Do',
                'category'   => 'Puppy & Dog Problems',
                'sort_order' => 170,
                'content'    => <<<'HTML'
<h2>Digging — Why It Happens & What to Do</h2>
<p>Digging is a natural, instinctive behaviour for many dogs. Terriers, Dachshunds, Huskies, and several other breeds were specifically bred to dig — it's in their DNA. But even dogs without this heritage may dig for a variety of reasons.</p>

<h3>Common Causes of Digging</h3>
<ul>
  <li><strong>Boredom or excess energy</strong> — the most common cause. A dog with nothing to do will often dig.</li>
  <li><strong>Heat</strong> — dogs dig to reach cool earth on hot days.</li>
  <li><strong>Prey instincts</strong> — following the scent of a mole, rodent, or insect.</li>
  <li><strong>Escape attempts</strong> — dogs that are bored, anxious, or frustrated may try to dig under fences.</li>
  <li><strong>Comfort seeking</strong> — pregnant or pseudo-pregnant females may dig as nesting behaviour.</li>
</ul>

<h3>Solutions</h3>
<ul>
  <li><strong>Increase exercise and enrichment</strong> — a tired dog digs less. Ensure adequate daily walks and mental stimulation.</li>
  <li><strong>Create a digging zone</strong> — designate one area of the garden (a sandpit, for example) where digging is permitted. Bury toys or treats to encourage use of the designated area.</li>
  <li><strong>Block access</strong> to prized garden beds with barriers.</li>
  <li><strong>Supervise outdoor time</strong> so you can redirect digging as it happens.</li>
  <li>Provide shade and fresh water to reduce heat-related digging.</li>
</ul>

<h3>Escape Diggers</h3>
<p>If your dog is digging along the fence line, address the underlying cause (boredom, anxiety, or an undesireable attraction on the other side). Bury chicken wire along the base of the fence as a physical deterrent.</p>
HTML,
            ],

            [
                'title'      => 'Feeding Your New Puppy',
                'category'   => 'Puppy & Dog Problems',
                'sort_order' => 180,
                'content'    => <<<'HTML'
<h2>Feeding Your New Puppy</h2>
<p>Good nutrition is foundational to your puppy's growth, development, and long-term health. The right diet during puppyhood sets the stage for a healthy adult dog.</p>

<h3>Choosing a Food</h3>
<ul>
  <li>Feed a <strong>complete and balanced puppy food</strong> specifically formulated for your puppy's size category (small breed, medium breed, large breed). Large and giant breed puppies have different calcium/phosphorus requirements to prevent developmental joint problems.</li>
  <li>Look for a named meat (chicken, beef, lamb) as the first ingredient.</li>
  <li>Discuss food choice with your veterinarian if unsure.</li>
</ul>

<h3>How Much and How Often</h3>
<ul>
  <li><strong>8–12 weeks:</strong> 4 meals per day</li>
  <li><strong>12–16 weeks:</strong> 3 meals per day</li>
  <li><strong>4 months onwards:</strong> 2 meals per day</li>
  <li>Follow the feeding guide on your chosen food — it varies by brand. Adjust for body condition (you should be able to feel ribs easily but not see them).</li>
</ul>

<h3>Transitioning Foods</h3>
<p>When changing your puppy's food, transition gradually over 7–10 days by mixing increasing proportions of the new food with the old. Sudden changes can cause digestive upset.</p>

<h3>Table Scraps & Human Food</h3>
<ul>
  <li>Many human foods are toxic to dogs: chocolate, grapes, raisins, onions, garlic (in large quantities), macadamia nuts, xylitol (found in sugar-free products), and alcohol.</li>
  <li>Avoid feeding from the table — it encourages begging and can cause digestive issues.</li>
</ul>

<h3>Fresh Water</h3>
<p>Clean, fresh water must be available at all times. Check and refill the water bowl daily.</p>
HTML,
            ],

            [
                'title'      => 'Sleeping Arrangements for Your Puppy',
                'category'   => 'Puppy & Dog Problems',
                'sort_order' => 190,
                'content'    => <<<'HTML'
<h2>Sleeping Arrangements for Your Puppy</h2>
<p>Where your puppy sleeps is a personal choice, but it's worth thinking through before bringing them home — because wherever you start is what they'll expect going forward.</p>

<h3>The First Few Nights</h3>
<p>The first nights away from their mother and littermates are stressful for most puppies. Some whining and crying is normal. Resist the urge to give in to constant crying — if you go to them every time they make noise, you teach them that crying brings company.</p>

<h3>Options</h3>
<p><strong>Crate in your bedroom:</strong> Many trainers recommend starting with the crate next to your bed. You can reassure the puppy with your voice without getting up, and they settle more quickly. Once settled, the crate can be moved to its permanent location gradually.</p>

<p><strong>Crate outside the bedroom:</strong> Some puppies settle well alone from the start. Place a ticking clock or a warmed (not hot) wheat bag near the crate to mimic littermate warmth.</p>

<p><strong>On the bed:</strong> This is a personal preference. It is not "bad" for your dog's behaviour or status — the idea that dogs who sleep on the bed become dominant is not supported by modern behavioural science. The practical concerns are shedding, size, and toilet training (you may not hear them needing to go out at night).</p>

<h3>Consistency Is Key</h3>
<p>Decide before the puppy arrives where they will sleep and stick to it. Mixed messages make settling in harder for the puppy.</p>
HTML,
            ],

            [
                'title'      => 'Integrating Your New Puppy with Resident Dogs',
                'category'   => 'Puppy & Dog Problems',
                'sort_order' => 200,
                'content'    => <<<'HTML'
<h2>Integrating Your New Puppy with Resident Dogs</h2>
<p>Introducing a new puppy to a resident dog requires careful management. Even the most dog-friendly adult dog can be overwhelmed by a persistent, bouncy puppy. Done well, integration can be smooth and rewarding for everyone.</p>

<h3>Before You Bring the Puppy Home</h3>
<ul>
  <li>Prepare separate spaces — the puppy and resident dog should each have their own safe area initially.</li>
  <li>Consider swapping bedding before the introduction so the dogs can become familiar with each other's scent.</li>
</ul>

<h3>The First Meeting</h3>
<ul>
  <li>Ideally, the first meeting should be on neutral ground (not in the resident dog's home territory).</li>
  <li>Keep both dogs on lead but allow them to approach and sniff briefly. Keep leads loose — tight leads communicate tension.</li>
  <li>Watch for relaxed, curious body language. If either dog shows fear or aggression, create more distance and try again.</li>
  <li>Keep the first interaction short and positive.</li>
</ul>

<h3>At Home</h3>
<ul>
  <li>Supervise all interactions until you are confident the dogs are comfortable together.</li>
  <li>Give the resident dog space to retreat away from the puppy. A stairgate or crate gives them sanctuary.</li>
  <li>Feed separately to avoid resource guarding.</li>
  <li>Give the resident dog one-on-one time and attention so they don't feel replaced.</li>
  <li>Do not leave them unsupervised until you are absolutely sure it is safe.</li>
</ul>

<h3>Resident Dog's Role</h3>
<p>It is normal for the resident dog to correct the puppy with a growl or snap. This is appropriate canine communication and teaches the puppy manners. Do not punish the resident dog for this — but do step in if corrections escalate beyond a brief warning.</p>
HTML,
            ],

            [
                'title'      => 'Separation Anxiety — Recognition and Management',
                'category'   => 'Puppy & Dog Problems',
                'sort_order' => 210,
                'content'    => <<<'HTML'
<h2>Separation Anxiety — Recognition and Management</h2>
<p>Separation anxiety is one of the most common and distressing behavioural problems in dogs. It occurs when a dog becomes extremely distressed at being left alone or separated from their attachment figure.</p>

<h3>Signs of Separation Anxiety</h3>
<ul>
  <li>Excessive barking, howling, or whining shortly after the owner leaves</li>
  <li>Destructive behaviour (chewing, scratching at doors and windows)</li>
  <li>Toileting indoors despite being housetrained</li>
  <li>Excessive drooling, panting, or pacing</li>
  <li>Attempting to escape</li>
  <li>Over-excitement or clinginess when the owner returns</li>
</ul>

<h3>Causes</h3>
<p>Some dogs are more predisposed to separation anxiety: dogs that have never been taught to be alone, those that have experienced a change in routine or household, rescue dogs, and certain sensitive breeds.</p>

<h3>Prevention: Teaching Your Puppy to Be Alone</h3>
<ul>
  <li>From the start, practise short absences and return before the puppy becomes distressed.</li>
  <li>Gradually increase the duration of absences.</li>
  <li>Ensure your puppy has a safe, comfortable space (like a crate) that is associated with positive experiences.</li>
  <li>Avoid making departures and arrivals emotionally charged events.</li>
</ul>

<h3>Management for Established Anxiety</h3>
<ul>
  <li>Desensitisation: systematic, very gradual exposure to being alone.</li>
  <li>Counter-conditioning: making alone time associated with good things (a stuffed Kong, for example).</li>
  <li>Ensure adequate exercise before leaving the dog alone.</li>
  <li>Consider a dog-walker, doggy daycare, or a companion dog for severe cases.</li>
</ul>

<p><strong>Note:</strong> Severe separation anxiety often requires professional help from a behaviourist or veterinary behaviourist. Do not hesitate to seek help — it is a welfare issue for the dog.</p>
HTML,
            ],

            // ─────────────────────────────────────────────
            // OBEDIENCE POINTERS
            // ─────────────────────────────────────────────
            [
                'title'      => 'Pulling on the Lead — The Red Light Green Light System',
                'category'   => 'Obedience Pointers',
                'sort_order' => 220,
                'content'    => <<<'HTML'
<h2>Pulling on the Lead — The Red Light Green Light System</h2>
<p>Pulling on the lead is one of the most common complaints from dog owners — and one of the most fixable with a simple, consistent technique.</p>

<h3>Why Dogs Pull</h3>
<p>Dogs pull because it works. The moment a pulling dog reaches the interesting thing they were heading towards, the pulling is reinforced. They learn: "pulling gets me places faster." Our job is to change that equation.</p>

<h3>The Red Light Green Light Method</h3>
<p>This technique is simple: the lead being tight means the walk stops (red light), and the walk only continues when the lead is loose (green light).</p>
<ol>
  <li>Walk as normal. The moment your dog pulls and the lead goes tight, <strong>stop completely</strong>.</li>
  <li>Stand still. Don't yank the lead, don't say anything. Just wait.</li>
  <li>The moment your dog turns towards you or takes a step back to release the tension, say "yes!" and begin walking again.</li>
  <li>Repeat consistently every single time the lead tightens.</li>
</ol>

<h3>What Makes This Work</h3>
<p>The dog learns: "tight lead = walk stops. Loose lead = walk continues." You are letting the dog's own behaviour control the consequence. It requires patience — walks will be slow at first — but consistency produces results.</p>

<h3>Tips</h3>
<ul>
  <li>Be consistent every time — if you allow pulling sometimes, the dog will keep trying.</li>
  <li>Reward your dog lavishly when they are walking nicely beside you.</li>
  <li>Practice in low-distraction environments first before tackling busy streets.</li>
  <li>A front-clip harness can help reduce pulling while you work on training.</li>
</ul>
HTML,
            ],

            [
                'title'      => 'Building Drive & Focus',
                'category'   => 'Obedience Pointers',
                'sort_order' => 230,
                'content'    => <<<'HTML'
<h2>Building Drive & Focus</h2>
<p>A dog that is enthusiastic about training and can focus on their handler in the presence of distractions is a joy to work with. This doesn't happen by accident — it is built deliberately.</p>

<h3>What Is Drive?</h3>
<p>Drive refers to a dog's motivation and enthusiasm for work. A high-drive dog is engaged, eager, and willing. We can amplify natural drive through how we train.</p>

<h3>Building Enthusiasm</h3>
<ul>
  <li><strong>Keep sessions short</strong> — always end before the dog is bored or tired.</li>
  <li><strong>Match reward to effort</strong> — high-value treats or favourite toys for difficult tasks.</li>
  <li><strong>Be exciting</strong> — your own energy and enthusiasm is contagious. A flat, bored handler produces a flat, bored dog.</li>
  <li><strong>Use variable reinforcement</strong> — once a behaviour is established, occasional jackpots (multiple treats) keep the dog guessing and engaged.</li>
</ul>

<h3>Building Focus</h3>
<p>Attention is a teachable behaviour. Begin with "eye contact" games: any time your dog looks at your face, mark and reward. Practise in calm environments, then gradually add distractions. A dog that can hold eye contact with you in a park full of other dogs has incredible focus.</p>

<h3>The Handler's Role</h3>
<p>You are the most interesting thing in your dog's world — or you should be. If your dog finds the environment consistently more rewarding than you, look at your rewards and your training approach. Make yourself worth listening to.</p>
HTML,
            ],

            [
                'title'      => 'What Your Dog Wants From You',
                'category'   => 'Obedience Pointers',
                'sort_order' => 240,
                'content'    => <<<'HTML'
<h2>What Your Dog Wants From You</h2>
<p>Understanding what your dog needs from the human–dog relationship helps you be a better handler, build a stronger bond, and address problems before they start.</p>

<h3>Consistency</h3>
<p>Dogs thrive on predictability. Consistent rules, consistent cue words, consistent responses to behaviour — this gives the dog a clear picture of what is expected and what to expect in return. When rules change or are applied inconsistently, dogs become confused and anxious.</p>

<h3>Clear Communication</h3>
<p>Dogs are not born understanding English. Every cue you use needs to be taught, one step at a time, with clear markers for right and wrong choices. The clearer your communication, the faster your dog learns.</p>

<h3>Physical and Mental Exercise</h3>
<p>A dog with unmet exercise and mental stimulation needs will find its own entertainment — usually destructive. Most behaviour problems are rooted in a dog that is simply not getting enough of what it needs.</p>

<h3>Safety</h3>
<p>Dogs need to feel safe. A dog that lives in fear — whether of strangers, loud noises, or unpredictable punishment from their owner — cannot learn well and cannot thrive.</p>

<h3>Positive Relationship</h3>
<p>Dogs are social animals that form deep bonds with their humans. They want your attention, your approval, and to be part of the family. Time spent training, playing, and simply being together is time well spent.</p>

<h3>Boundaries</h3>
<p>Contrary to old thinking, dogs don't fight for dominance over their owners. But they do benefit from clear boundaries that give structure to daily life. A dog that knows what is expected of them is a confident, secure dog.</p>
HTML,
            ],

            [
                'title'      => 'The Learning Plateau — Why Progress Stalls',
                'category'   => 'Obedience Pointers',
                'sort_order' => 250,
                'content'    => <<<'HTML'
<h2>The Learning Plateau — Why Progress Stalls</h2>
<p>Every handler experiences a period where training seems to stop working. The dog was doing so well, and now suddenly they "forget" everything. This is the learning plateau, and it is completely normal.</p>

<h3>What Is Happening</h3>
<p>Learning in dogs (and humans) doesn't progress in a straight line. After periods of rapid improvement, there are consolidation phases where the behaviour is being internalised and generalised. From the outside, it looks like regression — but internally, the brain is doing important work.</p>

<h3>Adolescence and the Plateau</h3>
<p>The most dramatic learning plateau usually coincides with adolescence (roughly 6–18 months). Hormonal changes and the drive to explore the environment compete with previously established training. This is when many owners give up — don't. This phase passes.</p>

<h3>What to Do</h3>
<ul>
  <li><strong>Go back to basics</strong> — revisit easy, well-established behaviours to remind the dog that training works and is rewarding.</li>
  <li><strong>Increase the value of your rewards</strong> — a plateau sometimes signals that your current rewards aren't motivating enough in the current environment.</li>
  <li><strong>Reduce distractions</strong> — if the environment has become harder, simplify it temporarily.</li>
  <li><strong>Increase consistency</strong> — plateaus are often reinforced by inconsistent practice.</li>
  <li><strong>Be patient</strong> — plateaus are temporary. Steady, positive work will produce results.</li>
</ul>
HTML,
            ],

            [
                'title'      => 'Weaning From Treats',
                'category'   => 'Obedience Pointers',
                'sort_order' => 260,
                'content'    => <<<'HTML'
<h2>Weaning From Treats</h2>
<p>A common worry among new dog owners is "will my dog only work for treats?" The answer is: only if you always give treats in the same predictable way. Weaning from treats is about moving from a continuous reinforcement schedule to a variable one.</p>

<h3>How to Wean Progressively</h3>
<ol>
  <li><strong>Establish the behaviour first</strong> — never wean from treats until the dog is performing the behaviour reliably. Reducing rewards too soon is the single most common training mistake.</li>
  <li><strong>Move to intermittent reinforcement</strong> — reward every other time, then every third time, then randomly. Variable reinforcement schedules are actually more powerful than continuous rewards — it's the same psychology behind gambling.</li>
  <li><strong>Replace food with other rewards</strong> — praise, pats, play, and real-life rewards ("good sit = I open the door and you get to go outside") can become the primary reinforcers.</li>
  <li><strong>Keep treats for new behaviours or increased difficulty</strong> — treats remain useful tools. There is no need to eliminate them entirely.</li>
</ol>

<h3>The Practical Reality</h3>
<p>Professional trainers and competition handlers use treats throughout their dogs' lives. There is nothing wrong with rewarding a dog with food. The goal is a dog that will also work reliably without a treat in your hand — not a treat-free dog.</p>
HTML,
            ],

            [
                'title'      => 'Getting Your Dog to Offer Behaviours',
                'category'   => 'Obedience Pointers',
                'sort_order' => 270,
                'content'    => <<<'HTML'
<h2>Getting Your Dog to Offer Behaviours</h2>
<p>One of the most exciting milestones in training is when your dog starts to actively offer behaviours without being asked — trying things to see what earns a reward. This is called "operant learning" and it produces a dog that is an active, engaged training partner.</p>

<h3>The 101 Things to Do With a Box Game</h3>
<p>One of the best exercises for developing an "offering" mindset is the classic box game: place an object (like a cardboard box) on the floor and reward your dog for any interaction with it. First a look, then a step towards it, then a nose touch, then a paw on it. Your dog quickly learns that engaging with the environment produces rewards — and begins to offer new behaviours spontaneously.</p>

<h3>Free Shaping</h3>
<p>Free shaping is training without luring or prompting. You wait for your dog to offer a movement or behaviour, then mark and reward it. Through incremental steps (successive approximations), you shape the final behaviour. It takes patience but produces an extremely engaged, thoughtful dog.</p>

<h3>Why This Matters</h3>
<ul>
  <li>Dogs that offer behaviours are easier to train new skills.</li>
  <li>It builds confidence — the dog learns that their choices have positive consequences.</li>
  <li>It combats the "shut down" effect sometimes seen in over-corrected dogs.</li>
  <li>It's excellent mental enrichment.</li>
</ul>
HTML,
            ],

            // ─────────────────────────────────────────────
            // CANINE BEHAVIOUR
            // ─────────────────────────────────────────────
            [
                'title'      => 'Getting Inside Your Puppy\'s Head',
                'category'   => 'Canine Behaviour',
                'sort_order' => 280,
                'content'    => <<<'HTML'
<h2>Getting Inside Your Puppy's Head</h2>
<p>Understanding how your puppy perceives and experiences the world helps you become a more empathetic and effective handler. Dogs are not furry humans — but they are also not the simple, hierarchy-driven animals of older training theory.</p>

<h3>The Dog's World</h3>
<p>Dogs experience the world primarily through smell. Their olfactory ability is estimated to be 10,000–100,000 times more sensitive than ours. A walk is not just exercise — it is an incredibly rich sensory experience. Allowing your dog to sniff on walks is not slowing you down; it is meeting a genuine behavioural need.</p>

<h3>Communication</h3>
<p>Dogs communicate predominantly through body language. Learning to read your dog's posture, facial expressions, ear position, tail carriage, and calming signals will transform your relationship. A tucked tail, lowered body, and turned head are not stubbornness — they are stress signals that deserve a compassionate response.</p>

<h3>Calming Signals</h3>
<p>Turid Rugaas, a Norwegian dog trainer, documented over 30 "calming signals" — behaviours dogs use to de-escalate tension and communicate discomfort. Common ones include yawning, lip licking, looking away, slowing down, and sniffing the ground. When your dog displays these around other dogs or people, they are communicating something important.</p>

<h3>Empathy in Training</h3>
<p>Asking why your dog is doing something — rather than simply reacting to what they're doing — produces better solutions. A dog that barks at strangers is not "bad." They are communicating fear or insecurity. Punishment won't fix that; understanding and addressing the root emotion will.</p>
HTML,
            ],

            [
                'title'      => 'Being a Good Leader for Your Dog',
                'category'   => 'Canine Behaviour',
                'sort_order' => 290,
                'content'    => <<<'HTML'
<h2>Being a Good Leader for Your Dog</h2>
<p>The concept of "dominance" in dog training has been largely debunked by modern science. Dogs do not constantly scheme to take over the household. What they do need, however, is a calm, consistent, and trustworthy guide — a good leader.</p>

<h3>What Good Leadership Looks Like</h3>
<ul>
  <li><strong>Consistency</strong> — the same rules apply every day. What was not allowed yesterday is not allowed today.</li>
  <li><strong>Calm authority</strong> — a leader is not the loudest voice in the room. Calm, confident energy communicates reliability to a dog.</li>
  <li><strong>Predictability</strong> — routines give dogs a sense of security. Predictable feeding times, walk times, and sleep times reduce anxiety.</li>
  <li><strong>Decision-making</strong> — the leader controls access to good things (food, play, walks) and does so fairly and consistently.</li>
  <li><strong>Protection</strong> — a good leader protects their dog from things that frighten or overwhelm them, rather than forcing exposure.</li>
</ul>

<h3>What Good Leadership Is NOT</h3>
<ul>
  <li>Physical dominance — pushing dogs off furniture, pinning them, alpha rolls. These methods cause fear and can trigger aggression.</li>
  <li>Unpredictability — reacting with anger sometimes and ignoring the same behaviour other times.</li>
  <li>Permissiveness — allowing all behaviour without structure.</li>
</ul>

<h3>The Result</h3>
<p>A dog with a good leader is a secure, confident dog. They don't need to "take charge" because they trust their human to handle the world. This is the foundation of an enjoyable and safe human–dog relationship.</p>
HTML,
            ],

            [
                'title'      => 'The Golden Rules of Learning',
                'category'   => 'Canine Behaviour',
                'sort_order' => 300,
                'content'    => <<<'HTML'
<h2>The Golden Rules of Learning</h2>
<p>Whether you are teaching a new puppy or working with an adult dog, these principles apply every time.</p>

<ol>
  <li><strong>Timing is everything.</strong> The reward or marker must come within one second of the desired behaviour for the dog to make the connection. Late rewards are confusing rewards.</li>

  <li><strong>Set the dog up to succeed.</strong> Training should be structured so that the dog is getting it right more often than not. If your dog is failing repeatedly, the exercise is too hard — break it down into smaller steps.</li>

  <li><strong>One criterion at a time.</strong> Don't ask for distance, duration, and distraction all at once. Progress each dimension separately.</li>

  <li><strong>End on success.</strong> Always end a training session on a positive note, even if you have to go back to an easier exercise to do it.</li>

  <li><strong>If it's not working, change something.</strong> Doing the same thing and expecting a different result is not a training strategy. Change the environment, the reward, the approach, or the steps.</li>

  <li><strong>What gets reinforced gets repeated.</strong> Every time a behaviour produces a good outcome, it becomes more likely. Be intentional about what you are reinforcing.</li>

  <li><strong>Dogs learn from patterns, not lectures.</strong> Repetition in consistent conditions builds reliable behaviour. Ten short sessions produce better results than one long one.</li>

  <li><strong>Your relationship is the foundation.</strong> A dog that trusts and enjoys working with their handler will achieve more than one working under duress.</li>
</ol>
HTML,
            ],

            [
                'title'      => 'Why Does My Dog Do That?',
                'category'   => 'Canine Behaviour',
                'sort_order' => 310,
                'content'    => <<<'HTML'
<h2>Why Does My Dog Do That?</h2>
<p>Dogs do many things that seem odd to humans but make perfect sense from a canine perspective. Here are some common behaviours explained.</p>

<h3>Circling Before Lying Down</h3>
<p>An ancestral behaviour — wild dogs would circle to pat down grass and check for snakes or insects before settling. It's completely normal and harmless.</p>

<h3>Phantom Pregnancy</h3>
<p>Unspayed females may show signs of pregnancy (nesting, collecting toys, milk production, behavioural changes) without being pregnant. This is driven by progesterone and is a normal biological phenomenon in dogs. It usually resolves on its own, but consult your vet if it is severe or prolonged. Spaying after the false pregnancy ends will prevent recurrence.</p>

<h3>Territory Marking</h3>
<p>Leg-lifting and scent marking are natural canine communication behaviours. Dogs use urine to "post notices" — communicating their presence, sex, and reproductive status to other dogs. In intact males, this can be very frequent. Neutering often reduces but does not always eliminate marking.</p>

<h3>Chasing Cats</h3>
<p>Prey drive — the instinct to chase moving objects — is highly variable between breeds and individuals. Management (leads, secure gardens, careful introductions) and early socialisation with cats are the best approaches. Some dogs can be taught to ignore cats; others require lifelong management.</p>

<h3>Reacting to the Post / Hating Strangers at the Door</h3>
<p>From the dog's perspective, their barking has always worked — the stranger always leaves after the barking starts. This creates a deeply reinforced behaviour. Management (blocking visual access), counter-conditioning (delivering treats when the post arrives), and teaching an incompatible behaviour (go to your bed) are all effective approaches.</p>
HTML,
            ],

            [
                'title'      => 'Respecting Space & The Yellow Dog Project',
                'category'   => 'Canine Behaviour',
                'sort_order' => 320,
                'content'    => <<<'HTML'
<h2>Respecting Space & The Yellow Dog Project</h2>
<p>Not all dogs want to be greeted by strangers or other dogs. Learning to read signals and respect space is an important part of responsible dog ownership.</p>

<h3>Reading the Signs</h3>
<p>Before approaching an unfamiliar dog, look for signs of discomfort:</p>
<ul>
  <li>Body held low or tucked away</li>
  <li>Whale eye (whites of eyes visible)</li>
  <li>Lip licking, yawning, or turning away</li>
  <li>Stiff posture or hackles up</li>
</ul>
<p>If you see any of these, give the dog space. Always ask the owner before approaching their dog.</p>

<h3>Teaching Your Dog Greeting Manners</h3>
<ul>
  <li>Teach your dog to sit for greetings — reward calm behaviour around other dogs and people.</li>
  <li>Do not allow your dog to rush up to other dogs. "Friendly" does not mean "welcome."</li>
  <li>A dog rushing at another on a tight lead is one of the most common causes of reactive behaviour development.</li>
</ul>

<h3>The Yellow Dog Project</h3>
<p>The Yellow Dog Project is an international initiative to raise awareness that some dogs need space. Dogs wearing a <strong>yellow ribbon, bandana, or vest</strong> are communicating that they need extra space — they may be recovering from illness or surgery, in training, fearful, reactive, or elderly. The message is: "please do not approach without asking."</p>
<p>If you have a dog that needs space, a yellow ribbon is a simple, internationally recognised way to communicate that to other dog owners.</p>
HTML,
            ],

            // ─────────────────────────────────────────────
            // CANINE CARE
            // ─────────────────────────────────────────────
            [
                'title'      => 'Crate Training — A Safe Den for Your Dog',
                'category'   => 'Canine Care',
                'sort_order' => 330,
                'content'    => <<<'HTML'
<h2>Crate Training — A Safe Den for Your Dog</h2>
<p>A crate is not a punishment. Done correctly, crate training gives your dog a safe, personal space — a den they can retreat to voluntarily. Crated dogs are safer when travelling, recover faster from injuries, and are easier to housetrain.</p>

<h3>Choosing a Crate</h3>
<ul>
  <li>The crate should be just large enough for the dog to stand up, turn around, and lie down comfortably.</li>
  <li>If the puppy will grow significantly, use a crate with a divider to reduce the space temporarily.</li>
  <li>Wire crates allow good ventilation and visibility; covered crates feel more den-like for some dogs. Many dogs appreciate a blanket over the top and sides.</li>
</ul>

<h3>Introducing the Crate</h3>
<ol>
  <li>Place the crate in a busy area of the house with the door open. Put comfortable bedding and a favourite toy inside.</li>
  <li>Let the dog explore it at their own pace. Drop treats near, then inside the crate. Do not force entry.</li>
  <li>Begin feeding meals inside the crate with the door open. Once comfortable eating inside, close the door briefly while they eat, then open it before they finish. Gradually extend the time.</li>
  <li>Build up to closing the door for short periods while you are in the room, then while you leave the room, then while you leave the house.</li>
</ol>

<h3>Rules</h3>
<ul>
  <li>Never use the crate as punishment — the crate must always be a positive place.</li>
  <li>Puppies under 6 months should not be crated for more than 2–3 hours at a time (excluding overnight sleeping).</li>
  <li>Always ensure your dog has had a toilet opportunity and exercise before crating.</li>
</ul>
HTML,
            ],

            [
                'title'      => 'The Veterinarian — Your New Best Friend',
                'category'   => 'Canine Care',
                'sort_order' => 340,
                'content'    => <<<'HTML'
<h2>The Veterinarian — Your New Best Friend</h2>
<p>A good relationship with a veterinarian is one of the most important things you can establish for your dog's health and longevity. Don't wait until something is wrong to build that relationship.</p>

<h3>Choosing a Vet</h3>
<ul>
  <li>Ask for recommendations from friends, family, or your dog trainer.</li>
  <li>Visit the practice before registering — does the staff seem knowledgeable and caring? Is the environment clean and calm?</li>
  <li>Consider proximity — you want a vet you can reach quickly in an emergency.</li>
</ul>

<h3>First Visit</h3>
<p>Book a "meet and greet" visit shortly after getting your puppy. The goal is a positive experience — the vet examines the puppy, everyone gives treats and fuss, and the puppy leaves with a good impression. Building positive associations with the vet early prevents lifelong vet phobia.</p>

<h3>Routine Veterinary Care</h3>
<ul>
  <li>Annual health checks and booster vaccinations</li>
  <li>Dental assessments</li>
  <li>Parasite prevention (ticks, fleas, worms)</li>
  <li>Weight and nutrition monitoring</li>
  <li>Microchipping and tag registration</li>
  <li>Spay/neuter discussion</li>
</ul>

<h3>When to Seek Urgent Care</h3>
<p>Seek veterinary care immediately for: bloat (distended abdomen, unproductive retching), collapse, difficulty breathing, suspected poisoning, seizures, eye injuries, severe lameness, or any rapid deterioration in condition.</p>

<p><strong>Tip:</strong> Keep your vet's number and the number of your nearest emergency animal hospital in your phone. You don't want to be searching in a crisis.</p>
HTML,
            ],

            [
                'title'      => 'Nutrition — Feeding Your Dog Well',
                'category'   => 'Canine Care',
                'sort_order' => 350,
                'content'    => <<<'HTML'
<h2>Nutrition — Feeding Your Dog Well</h2>
<p>Good nutrition is one of the most powerful tools available to you as a dog owner. What you feed your dog affects their coat condition, energy levels, immune function, dental health, and longevity.</p>

<h3>Types of Dog Food</h3>
<p><strong>Dry kibble:</strong> Convenient, affordable, and nutritionally complete if you choose a quality brand. Look for a named meat source as the first ingredient, minimal fillers (corn, soy), and AAFCO or FEDIAF nutritional adequacy statement.</p>

<p><strong>Wet/canned food:</strong> Higher moisture content, often more palatable. Can be used alone or mixed with kibble. Tends to be more expensive and can contribute to dental tartar.</p>

<p><strong>Raw feeding (BARF):</strong> Biologically Appropriate Raw Food — raw meat, bones, and vegetables. Advocates claim health benefits; risks include bacterial contamination, nutritional imbalance if not properly formulated, and bone hazards. Research thoroughly and consult your vet before starting.</p>

<p><strong>Home-cooked:</strong> Time-intensive and difficult to balance without specialist knowledge. Consult a veterinary nutritionist if you want to pursue this.</p>

<h3>Life Stage Feeding</h3>
<ul>
  <li>Puppies: feed a food formulated for growth/all life stages.</li>
  <li>Adults: maintenance formula suitable for the dog's activity level.</li>
  <li>Seniors (7+ years): senior formulas with joint support and adjusted calorie levels.</li>
</ul>

<h3>Obesity</h3>
<p>Obesity is one of the most common preventable health problems in dogs. An overweight dog is at higher risk of arthritis, diabetes, heart disease, and a shortened lifespan. You should be able to easily feel (but not see) your dog's ribs. Consult your vet about an appropriate weight management plan if needed.</p>
HTML,
            ],

            [
                'title'      => 'Tick & Flea Control',
                'category'   => 'Canine Care',
                'sort_order' => 360,
                'content'    => <<<'HTML'
<h2>Tick & Flea Control</h2>
<p>South Africa has several tick species that pose serious health risks to dogs, including the brown dog tick (transmits biliary/babesiosis) and the yellow dog tick. Fleas cause skin irritation, allergic reactions, and can carry tapeworms. Year-round prevention is strongly recommended.</p>

<h3>Tick-Borne Biliary (Babesiosis)</h3>
<p>Biliary is a potentially life-threatening red blood cell parasite transmitted by ticks. Signs include lethargy, pale/yellow gums, dark urine, and collapse. It is a veterinary emergency. Prevention through regular tick control is essential.</p>

<h3>Prevention Options</h3>
<ul>
  <li><strong>Spot-on treatments</strong> (Frontline, Bravecto, Advocate) — applied to the skin on the back of the neck, typically monthly or every 3 months.</li>
  <li><strong>Oral treatments</strong> (Nexgard, Bravecto chews, Simparica) — chewable tablets, often monthly or quarterly. Highly effective.</li>
  <li><strong>Tick collars</strong> (Seresto) — effective for up to 8 months.</li>
  <li><strong>Tick sprays and dips</strong> — can be used as an additional measure.</li>
</ul>

<h3>Tick Checks</h3>
<p>After any time in grassland, bush, or areas frequented by other dogs, run your hands through your dog's coat and check particularly around ears, between toes, under collar, groin, and armpits. Remove ticks by grasping as close to the skin as possible with fine-tipped tweezers and pulling straight out (do not twist).</p>

<h3>Environmental Control</h3>
<p>Fleas spend most of their life cycle off the dog — in bedding, carpets, and soft furnishings. Treat the environment (vacuuming, environmental sprays) as well as the dog.</p>
HTML,
            ],

            [
                'title'      => 'The Importance of Vaccinations',
                'category'   => 'Canine Care',
                'sort_order' => 370,
                'content'    => <<<'HTML'
<h2>The Importance of Vaccinations</h2>
<p>Vaccinations protect your dog from potentially fatal infectious diseases. They are one of the single most effective health interventions available.</p>

<h3>Core Vaccines</h3>
<p>Core vaccines are recommended for all dogs regardless of lifestyle:</p>
<ul>
  <li><strong>Distemper</strong> — a serious viral illness affecting the respiratory, gastrointestinal, and nervous systems.</li>
  <li><strong>Parvovirus</strong> — a highly contagious and often fatal disease, particularly in puppies. The virus survives in the environment for months.</li>
  <li><strong>Adenovirus (Hepatitis)</strong> — causes liver disease.</li>
  <li><strong>Parainfluenza</strong> — a component of "kennel cough."</li>
</ul>

<h3>Non-Core Vaccines</h3>
<p>Recommended based on lifestyle and risk:</p>
<ul>
  <li><strong>Rabies</strong> — required by law in many areas and for any dog travelling internationally.</li>
  <li><strong>Bordetella (Kennel Cough)</strong> — recommended for dogs that attend kennels, training classes, or dog parks.</li>
</ul>

<h3>Puppy Schedule</h3>
<ul>
  <li>6–8 weeks: First puppy vaccination</li>
  <li>10–12 weeks: Second puppy vaccination</li>
  <li>14–16 weeks: Third puppy vaccination</li>
  <li>Annual: Booster vaccinations</li>
</ul>

<h3>Maternal Immunity & Why Multiple Doses Are Needed</h3>
<p>Puppies receive maternal antibodies from their mother's colostrum, which can interfere with vaccination. Because we cannot predict exactly when maternal immunity wanes, a series of vaccinations is given to ensure at least one "takes" at the right time.</p>

<p><strong>Note:</strong> Until the puppy's vaccination course is complete, avoid contact with unvaccinated dogs and high-risk environments (dog parks, communal areas). Puppy school run by reputable trainers requires proof of vaccination.</p>
HTML,
            ],

            [
                'title'      => 'Pet Identification — Keeping Your Dog Safe',
                'category'   => 'Canine Care',
                'sort_order' => 380,
                'content'    => <<<'HTML'
<h2>Pet Identification — Keeping Your Dog Safe</h2>
<p>Every dog should carry permanent identification. If your dog is ever lost or stolen, proper identification is the most reliable path to a reunion.</p>

<h3>Collar & Identity Disc</h3>
<p>Your dog should wear a collar with an identity tag at all times when outside (or even indoors if they are at risk of escaping). The tag should include your name and contact number. This is the fastest way to reunite a lost dog with its owner.</p>
<ul>
  <li>Check the collar fit regularly — especially for growing puppies. You should be able to slide two fingers under the collar.</li>
  <li>Engraved metal tags are more durable than printed plastic.</li>
</ul>

<h3>Microchipping</h3>
<p>A microchip is a permanent form of identification — about the size of a grain of rice — injected under the skin between the shoulder blades. It contains a unique number that, when scanned, links to your contact details on a national registry. Microchipping is a painless, one-time procedure done by a veterinarian.</p>
<ul>
  <li>Register your chip on the national database (NSPCA, SAVA registry, or equivalent).</li>
  <li>Update your contact details on the registry if you move or change your number.</li>
</ul>

<h3>Tattoo</h3>
<p>Tattooing (usually an identity number in the ear flap) is an older form of identification still used by some breeders and shelters. It is permanent and visible without equipment but can fade or become illegible over time.</p>

<h3>Using All Three</h3>
<p>The most secure approach is to use all three forms of identification: collar tag (immediately visible), microchip (permanent and scannable), and registration with a lost-and-found pet network. This gives your dog the best possible chance of coming home.</p>
HTML,
            ],

            [
                'title'      => 'Common Household Dangers for Dogs',
                'category'   => 'Canine Care',
                'sort_order' => 390,
                'content'    => <<<'HTML'
<h2>Common Household Dangers for Dogs</h2>
<p>Many everyday items in the home pose serious risks to dogs. Awareness and prevention can save your dog's life.</p>

<h3>Toxic Foods</h3>
<ul>
  <li><strong>Chocolate</strong> — contains theobromine, which is toxic to dogs. Dark chocolate is the most dangerous.</li>
  <li><strong>Grapes & raisins</strong> — can cause kidney failure. The toxic substance is unknown and the dose-response is unpredictable.</li>
  <li><strong>Onions & garlic</strong> — in sufficient quantities, cause red blood cell damage.</li>
  <li><strong>Macadamia nuts</strong> — cause weakness, vomiting, tremors.</li>
  <li><strong>Xylitol</strong> — an artificial sweetener found in sugar-free gum, some peanut butters, and baked goods. Causes severe hypoglycaemia and liver failure.</li>
  <li><strong>Avocado</strong> — contains persin, which can cause vomiting and diarrhoea.</li>
  <li><strong>Alcohol</strong> — toxic in even small amounts.</li>
</ul>

<h3>Toxic Plants</h3>
<ul>
  <li>Sago palm (highly toxic — liver failure)</li>
  <li>Oleander</li>
  <li>Lily of the valley</li>
  <li>Tulip and daffodil bulbs</li>
  <li>Dieffenbachia (dumb cane)</li>
  <li>Aloe vera (gastrointestinal upset)</li>
</ul>
<p>Research any new plant before introducing it into a home with dogs.</p>

<h3>Household Chemicals</h3>
<ul>
  <li>Cleaning products, pesticides, and rodenticides</li>
  <li>Human medications (paracetamol, ibuprofen, and many others are toxic to dogs)</li>
  <li>Essential oils (tea tree, eucalyptus, and pennyroyal are particularly toxic)</li>
</ul>

<h3>Physical Hazards</h3>
<ul>
  <li>Loose electrical cables (chewing risk)</li>
  <li>Swimming pools — ensure puppies cannot fall in unsupervised</li>
  <li>Cooked bones — can splinter and cause internal injuries or blockages</li>
  <li>Small objects that can be swallowed (children's toys, batteries)</li>
</ul>

<p>If you suspect your dog has ingested a toxin, contact your veterinarian immediately. Do not wait for symptoms to appear.</p>
HTML,
            ],

            [
                'title'      => 'Gastric Torsion (Bloat) — A Dog Emergency',
                'category'   => 'Canine Care',
                'sort_order' => 400,
                'content'    => <<<'HTML'
<h2>Gastric Torsion (Bloat) — A Dog Emergency</h2>
<p>Gastric dilatation-volvulus (GDV), commonly called bloat, is one of the most rapidly life-threatening conditions a dog can experience. It is a true emergency requiring immediate veterinary intervention.</p>

<h3>What Happens</h3>
<p>The stomach fills with gas and/or fluid and twists on itself, cutting off blood supply to the stomach and spleen. Without treatment, a dog can die within hours. Even with emergency surgery, mortality rates are significant.</p>

<h3>Dogs at Risk</h3>
<p>Deep-chested, large and giant breed dogs are most at risk: Great Danes, German Shepherds, Standard Poodles, Dobermanns, Boxers, Setters, Weimaraners. Middle-aged to older dogs are at higher risk. Having a first-degree relative with GDV is a significant risk factor.</p>

<h3>Signs of GDV</h3>
<ul>
  <li>Unproductive retching or attempted vomiting (bringing up nothing or just saliva)</li>
  <li>Rapidly distending, drum-like abdomen</li>
  <li>Restlessness and obvious distress</li>
  <li>Pale gums</li>
  <li>Drooling excessively</li>
  <li>Collapse</li>
</ul>

<h3>Action</h3>
<p><strong>Go to an emergency vet immediately.</strong> This is not a "wait and see" condition. Every minute counts.</p>

<h3>Prevention</h3>
<ul>
  <li>Feed large breed dogs 2–3 smaller meals per day rather than one large meal.</li>
  <li>Avoid vigorous exercise for at least 1 hour before and after eating.</li>
  <li>Avoid elevated feeding bowls (research suggests these may increase risk).</li>
  <li>For very high-risk dogs, discuss prophylactic gastropexy with your veterinarian — a surgical procedure that permanently tacks the stomach to the abdominal wall to prevent twisting.</li>
</ul>
HTML,
            ],

            [
                'title'      => 'Fireworks & Noise Anxiety',
                'category'   => 'Canine Care',
                'sort_order' => 410,
                'content'    => <<<'HTML'
<h2>Fireworks & Noise Anxiety</h2>
<p>Fear of fireworks, thunder, and other sudden loud noises is extremely common in dogs. For some, it is a mild startle. For others, it is sheer terror. Understanding and preparation can make a significant difference.</p>

<h3>Why Dogs Fear Loud Noises</h3>
<p>Dogs hear at a much wider frequency range and at greater distances than humans. What is a bang to us can be an overwhelming assault on their senses. Some dogs may have had a frightening early experience with loud noises; others develop noise phobia through sensitisation (each exposure increases the fear).</p>

<h3>Signs of Noise Anxiety</h3>
<ul>
  <li>Trembling, shaking</li>
  <li>Panting and drooling</li>
  <li>Hiding, clinging to owner</li>
  <li>Barking and howling</li>
  <li>Destructive behaviour</li>
  <li>Toileting indoors</li>
  <li>Attempting to escape</li>
</ul>

<h3>Management During Fireworks/Thunder</h3>
<ul>
  <li>Keep your dog indoors and ensure all doors, windows, and gates are secure — panicking dogs frequently escape.</li>
  <li>Create a "safe den" — cover a crate with blankets to muffle sound. Allow the dog to go there voluntarily.</li>
  <li>Play calming music or white noise to mask the sounds (there are excellent playlists specifically created for this).</li>
  <li>Do not punish fearful behaviour — this makes it worse. It is okay to comfort your dog; you cannot reinforce fear with comfort.</li>
  <li>Act normally and calmly yourself — your composure is reassuring to your dog.</li>
  <li>Close curtains to reduce the visual flash of fireworks.</li>
</ul>

<h3>Long-Term Solutions</h3>
<ul>
  <li><strong>Desensitisation</strong> — gradual exposure to recorded sounds at very low volumes, paired with rewards, building up over weeks.</li>
  <li><strong>Anxiolytic aids</strong> — Adaptil (synthetic dog-appeasing pheromone), Thundershirts, calming supplements.</li>
  <li><strong>Veterinary medication</strong> — for severe cases, your vet can prescribe medication for use on anticipated high-anxiety nights.</li>
</ul>

<p>Start desensitisation work well in advance of fireworks season — not in the days before.</p>
HTML,
            ],

            [
                'title'      => 'Managing Guests & Your Dog',
                'category'   => 'Canine Care',
                'sort_order' => 420,
                'content'    => <<<'HTML'
<h2>Managing Guests & Your Dog</h2>
<p>Visitors to the home can be challenging for dogs — exciting for some, frightening for others. Good management ensures guests are comfortable and your dog is not overwhelmed.</p>

<h3>The Excitable Greeter</h3>
<p>Dogs that jump, bark excitedly, and rush guests need a structured greeting routine:</p>
<ul>
  <li>Teach "four on the floor" — only pet the dog when all four feet are on the ground.</li>
  <li>Ask guests to turn away and ignore the dog until they are calm. All attention comes when the dog is settled.</li>
  <li>A "go to your mat" or "wait" behaviour when the doorbell rings can transform arrivals from chaos to calm.</li>
</ul>

<h3>The Anxious or Fearful Dog</h3>
<ul>
  <li>Give the dog the option to retreat to a safe space — a bedroom or crate — if they choose.</li>
  <li>Ask guests not to approach, stare at, or force interaction with a fearful dog. Let the dog approach in their own time.</li>
  <li>Have guests offer treats by tossing them near the dog rather than directly from hand to avoid any conflict.</li>
  <li>Do not force a fearful dog into social situations — this increases fear.</li>
</ul>

<h3>Children and Dogs</h3>
<p>Children visiting with dogs requires extra vigilance:</p>
<ul>
  <li>Never leave children and dogs unsupervised, even if the dog is "gentle."</li>
  <li>Teach children to ask before touching any dog.</li>
  <li>Give the dog an escape route — never corner them.</li>
  <li>Recognise the early warning signs: stiff body, hard stare, lip lick, low growl. These are warnings before a bite.</li>
</ul>
HTML,
            ],

            // ─────────────────────────────────────────────
            // THEORY
            // ─────────────────────────────────────────────
            [
                'title'      => 'Learning Theory 101',
                'category'   => 'Theory',
                'sort_order' => 430,
                'content'    => <<<'HTML'
<h2>Learning Theory 101</h2>
<p>Dog training is applied animal learning theory. Understanding the basic science behind how dogs learn makes you a more effective, empathetic trainer.</p>

<h3>Classical Conditioning</h3>
<p>Discovered by Ivan Pavlov, classical conditioning is learning through association. A neutral stimulus (a bell) is repeatedly paired with an unconditioned stimulus (food) until the neutral stimulus alone produces a conditioned response (salivation). In dog training, this is how dogs learn that a clicker predicts a treat, and how environments and sounds become associated with emotions.</p>

<p>Relevance: A dog that has learned to associate the vet with pain will show fear responses before anything painful happens. Classical conditioning underpins all emotional responses.</p>

<h3>Operant Conditioning</h3>
<p>Developed by B.F. Skinner, operant conditioning describes how behaviour is shaped by its consequences. Behaviour that produces good outcomes is repeated; behaviour that produces bad outcomes is suppressed. This is the foundation of all deliberate dog training.</p>

<h3>The Four Quadrants</h3>
<ul>
  <li><strong>R+</strong> (positive reinforcement): Add good → behaviour increases</li>
  <li><strong>R–</strong> (negative reinforcement): Remove bad → behaviour increases</li>
  <li><strong>P+</strong> (positive punishment): Add bad → behaviour decreases</li>
  <li><strong>P–</strong> (negative punishment): Remove good → behaviour decreases</li>
</ul>

<h3>Extinction</h3>
<p>When a behaviour no longer produces any outcome (neither reward nor punishment), it eventually stops. This is called extinction. Note: before a behaviour goes extinct, it typically gets worse (an "extinction burst"). If you start ignoring jumping and the dog jumps more intensely, you're seeing an extinction burst — stay the course.</p>

<h3>Generalisation</h3>
<p>A behaviour learned in one context needs to be taught in multiple contexts before it is truly "generalised." A dog that sits perfectly in the kitchen may not sit at all at the park. This is not stubbornness — it is how learning works. Train in many different environments.</p>
HTML,
            ],

            [
                'title'      => 'Additional Teaching Tools — Markers & Bridges',
                'category'   => 'Theory',
                'sort_order' => 440,
                'content'    => <<<'HTML'
<h2>Additional Teaching Tools — Markers & Bridges</h2>
<p>Good trainers use a variety of communication tools to make training precise and clear. Understanding these tools gives you a significant training advantage.</p>

<h3>The Reward Marker (Mark)</h3>
<p>A reward marker is a precise, consistent signal — usually a clicker or a specific word like "yes!" — that tells the dog the exact moment they did the right thing and a reward is coming. The marker "bridges" the time between the behaviour and the delivery of the treat, which is why it is also called a bridge.</p>
<p>The marker must be:</p>
<ul>
  <li>Immediate — within one second of the behaviour</li>
  <li>Consistent — always the same signal</li>
  <li>Reliable — always followed by a reward (in the early stages)</li>
</ul>

<h3>The Clicker</h3>
<p>A clicker is a small, handheld device that produces a consistent click sound. Its precision and consistency make it a powerful training tool. Before using a clicker in training, it must be "charged" — paired with treats repeatedly so the dog learns the click means a reward is coming.</p>

<h3>The No Reward Marker (NRM)</h3>
<p>The NRM is a calm signal — often "ah-ah," "oops," or "try again" — that tells the dog the response wasn't correct and no reward is coming. It is <em>not</em> a punishment. It is information. The NRM should be delivered neutrally, without frustration, and should prompt the dog to try again.</p>

<h3>The Pre-Punishment Marker</h3>
<p>Used in some training programmes, this is a warning signal that indicates an aversive consequence is coming if the dog does not change its behaviour. At McKaynine, we use this sparingly and only in appropriate contexts. The pre-punishment marker only has value if it actually predicts a consequence — empty warnings teach the dog to ignore them.</p>

<h3>Putting It Together</h3>
<p>Good training communication sounds like this: ask for behaviour → dog performs → mark precisely → deliver reward. The clearer the feedback loop, the faster the dog learns and the more enjoyable training becomes for both of you.</p>
HTML,
            ],

        ];

        foreach ($articles as $article) {
            Resource::firstOrCreate(
                ['title' => $article['title']],
                array_merge($article, [
                    'is_published' => true,
                    'created_by'   => $adminId,
                ])
            );
        }

        $this->command->info('✓ Created ' . count($articles) . ' resource articles from McKaynine Owner\'s Guide.');
    }
}
