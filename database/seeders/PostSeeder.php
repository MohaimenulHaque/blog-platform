<?php

namespace Database\Seeders;

use App\Enums\PostStatus;
use App\Enums\PostVisibility;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    /**
     * The posts to seed.
     *
     * @var array<int, array{title: string, excerpt: string, category: string, author: string, tags: array<int, string>, views: int, body: array<int, array<string>>}>
     */
    public const POSTS = [
        [
            'title' => 'Why Laravel is Still the Best Way to Ship a Web App in 2026',
            'excerpt' => 'Ten years in, the framework\'s focus on developer happiness keeps paying dividends for teams of every size.',
            'category' => 'technology',
            'author' => 'sofia',
            'tags' => ['laravel', 'php', 'web-development'],
            'views' => 18420,
            'body' => [
                ['p' => 'Every few months a hot new framework promises to replace Laravel. And every few months, Laravel quietly adds another thousand production apps. There is a reason for that staying power, and it has little to do with hype.'],
                ['h2' => 'Convention over configuration, done right'],
                ['p' => 'Laravel borrowed the best ideas from Rails, Django and Symfony, then removed the friction. You get routing, ORM, migrations, queues, authentication and caching out of the box — all opinionated, all documented, all boring enough to trust in production.'],
                ['p' => 'That predictability is the real killer feature. A junior can land on a Laravel codebase and be productive within a week. Your tools should get out of the way between the idea and the working feature, and Laravel does exactly that.'],
                ['blockquote' => 'The best framework is not the one with the most features. It is the one whose defaults you stop thinking about.'],
                ['p' => 'Laravel\'s ecosystem — Inertia, Livewire, Filament, Sanctum, Octane — means you rarely reach for a second framework. The batteries are included, and they keep getting better.'],
                ['p' => 'None of this means Laravel is perfect. It has rough edges, and plenty of legitimate alternatives exist. But if your goal is to ship something real, the fastest path still runs straight through it.'],
            ],
        ],
        [
            'title' => 'The Pomodoro Technique is Broken. Here\'s What I Use Instead',
            'excerpt' => 'Twenty-five minute sprints interrupt the work that matters most. A simple timeboxing alternative keeps flow intact.',
            'category' => 'productivity',
            'author' => 'james',
            'tags' => ['focus', 'remote-work'],
            'views' => 24180,
            'body' => [
                ['p' => 'The Pomodoro technique assumes that the ideal unit of work is 25 minutes. In my experience, that assumption is wrong for almost everything except shallow admin tasks.'],
                ['h2' => 'Flow is the point'],
                ['p' => 'Deep work is a ramp. It takes fifteen minutes to reach the point where you are actually thinking well, and the timer ringing at minute 25 destroys exactly the momentum you spent the first fifteen building.'],
                ['p' => 'Instead, I timebox by outcome, not by duration. I ask: what is the smallest complete unit of this task, and roughly how long would a focused hour take? Then I block out ninety minutes, put my phone in another room, and commit to finishing that unit.'],
                ['blockquote' => 'The best productivity system is the one that protects your longest, most valuable blocks of attention.'],
                ['p' => 'Pomodoro remains useful in its place — clearing a cramped inbox, dealing with a backlog of small tickets. Use it for the small stuff. Give your real work the uninterrupted hours it deserves.'],
            ],
        ],
        [
            'title' => 'Designing Accessible Color Systems That Actually Work',
            'excerpt' => 'Accessibility is not a checklist item. A good token-driven color system makes contrast a default rather than an afterthought.',
            'category' => 'design',
            'author' => 'aisha',
            'tags' => ['css', 'ux-research'],
            'views' => 9803,
            'body' => [
                ['p' => 'Too many design systems treat accessibility as a compliance layer bolted on at the end. The better approach is to build contrast into the tokens themselves.'],
                ['h2' => 'Start with a contrast-first palette'],
                ['p' => 'Define your neutrals in pairs that are guaranteed to pass WCAG AA at their intended size. A dark surface, a light surface, and a single accessible mid-tone for borders means you can never accidentally ship text on a background with a ratio below 4.5:1.'],
                ['p' => 'Semantic tokens take this further. Instead of raw hex values, expose "text-default", "text-muted" and "border-default". Muted text stops being a style decision and becomes a deliberate, tested choice.'],
                ['blockquote' => 'If your color tokens can produce an inaccessible combination, the system has a bug — not the designer.'],
                ['p' => 'Finally, never rely on color alone to communicate state. Pair every color change with an icon, a label or a pattern shift. Your users with color vision deficiency will thank you, and so will everyone else on a poorly calibrated monitor.'],
            ],
        ],
        [
            'title' => 'The Surprising Science of Gratitude and Long-Term Happiness',
            'excerpt' => 'More than three decades of research point to one of the simplest interventions in psychology — and most of us never do it.',
            'category' => 'science',
            'author' => 'daniel',
            'tags' => ['data-science', 'wellbeing'],
            'views' => 12356,
            'body' => [
                ['p' => 'The gratitude literature is unusually robust. Across dozens of randomized trials, people who write down a few things they are grateful for report higher life satisfaction, better sleep and stronger relationships.'],
                ['h2' => 'What the longitudinal data shows'],
                ['p' => 'Long-term studies that follow participants over years find the effects are not just a novelty bump. The practice changes how people appraise ordinary events, training attention to notice positive things that were always there.'],
                ['p' => 'The mechanism appears to be reframing. Gratitude reliably shifts attention away from what is missing and toward what is present, and that shift compounds over time.'],
                ['blockquote' => 'Gratitude does not change your circumstances. It changes which circumstances you notice.'],
                ['p' => 'The practical takeaway is almost embarrassingly simple: three minutes, twice a week, writing three specific things. Specificity matters — "the coffee I had this morning" beats "my job" every time. The evidence says this works. The only failure mode is not doing it.'],
            ],
        ],
        [
            'title' => 'How to Ask for a Raise When Your Company Says Times Are Tough',
            'excerpt' => 'Bad timing is an excuse, not a refusal. A playbook for making the business case before you ever book the meeting.',
            'category' => 'career',
            'author' => 'priya',
            'tags' => ['career-growth', 'startups'],
            'views' => 30712,
            'body' => [
                ['p' => 'You deserve a raise, your budget is frozen, and your manager is dreading this conversation as much as you are. The meeting goes badly when the case is about need. It goes well when the case is about value.'],
                ['h2' => 'Build the case from market data first'],
                ['p' => 'Pull three salary benchmarks for your exact role, seniority and location. Frame your request as a range anchored in data, not a number plucked from hope. People negotiate against data far more comfortably than against feelings.'],
                ['p' => 'Then translate your last twelve months into revenue language. What shipped? What did it save? What did it enable? A one-page summary makes the conversation about your impact rather than your expenses.'],
                ['blockquote' => 'A raise is not a reward for loyalty. It is a price correction for value delivered.'],
                ['p' => 'If the budget genuinely cannot stretch this quarter, negotiate the roadmap: a defined milestone, a written target salary, and a date to revisit. Getting the commitment in writing is almost as valuable as the money.'],
            ],
        ],
        [
            'title' => 'Shedding Light on Dark Patterns: What Researchers Know',
            'excerpt' => 'The design industry has spent a decade perfecting interfaces that manipulate. A growing body of research is pushing back.',
            'category' => 'design',
            'author' => 'marco',
            'tags' => ['ux-research', 'security'],
            'views' => 8765,
            'body' => [
                ['p' => 'A dark pattern is a user interface carefully crafted to trick people into doing something they did not intend — subscribing, sharing data, staying on a page. Researchers now have a name, a taxonomy and mounting evidence for every tactic.'],
                ['h2' => 'The anatomy of manipulation'],
                ['p' => 'The most common patterns are the most boring: pre-checked boxes, trick questions, confusing cancel buttons and "roach motel" flows that are easy to enter and hard to leave. They work because they exploit defaults and the way attention lapses.'],
                ['p' => 'Regulators have noticed. The EU, US state regulators and a growing list of countries now treat the most egregious patterns as legally deceptive practices, with fines that grow every year.'],
                ['blockquote' => 'Dark patterns are a short-term growth hack and a long-term reputation tax.'],
                ['p' => 'The good news is that honest design is not a disadvantage. Companies that remove dark patterns report trust gains, and in trust-heavy industries the revenue follows. Deception is a strategy. It is just a bad one.'],
            ],
        ],
        [
            'title' => 'The CSS Features You\'ll Actually Use in 2026',
            'excerpt' => 'Container queries, native nesting and a genuinely good color function — modern CSS has quietly eliminated half your JavaScript.',
            'category' => 'technology',
            'author' => 'aisha',
            'tags' => ['css', 'web-development', 'javascript'],
            'views' => 15230,
            'body' => [
                ['p' => 'Every few years CSS undergoes a quiet revolution and nobody notices until the blog posts arrive. The current one is bigger than most.'],
                ['h2' => 'Nesting and container queries arrived to stay'],
                ['p' => 'Native CSS nesting means your component styles now read the way you always wanted them to, without a preprocessor in sight. Container queries let a component respond to its own width instead of the viewport — the missing piece for genuinely reusable cards.'],
                ['p' => 'The new color functions — oklch above all — give designers a perceptually uniform space where manipulating lightness and chroma behaves predictably across screens. Building accessible themes becomes engineering instead of guesswork.'],
                ['blockquote' => 'The best CSS is the CSS that removes a library.'],
                ['p' => 'Browsers have never been more consistent. Between nesting, container queries, subgrid and oklch, a striking amount of what used to require JavaScript or a framework is now a stylesheet. Spend an afternoon updating what you know; it pays off immediately.'],
            ],
        ],
        [
            'title' => 'Why Your Meetings Are Useless (and the One Rule That Fixes Them)',
            'excerpt' => 'Most meetings fail before anyone sits down because they lack a decision, an owner, or both.',
            'category' => 'productivity',
            'author' => 'james',
            'tags' => ['remote-work', 'focus'],
            'views' => 21340,
            'body' => [
                ['p' => 'The average professional spends eleven hours a week in meetings and calls most of them a waste of time. The problem is rarely the people. It is that the meeting has no output.'],
                ['h2' => 'The decision rule'],
                ['p' => 'Before you schedule a meeting, write down the decision that will be made in it, or the deliverable that will be produced. If you cannot name one, do not hold the meeting — use a document and async comments instead.'],
                ['p' => 'When a meeting does pass the test, add a named owner for the follow-up and agree on what done looks like before the call ends. Write it on the agenda during the meeting so there is no dispute later.'],
                ['blockquote' => 'A meeting without a decision is a lecture you have to attend.'],
                ['p' => 'The side effect is that your calendar clears itself. Meetings with real outputs tend to be shorter, more focused and far less frequent, and the quiet hours they free up are where the actual work happens.'],
            ],
        ],
        [
            'title' => 'Building a Small Data Pipeline You Won\'t Regret in a Year',
            'excerpt' => 'Most data pipelines are overengineered. A raw bucket, a scheduler and a good naming convention carry surprisingly far.',
            'category' => 'technology',
            'author' => 'sofia',
            'tags' => ['databases', 'cloud-computing', 'data-science'],
            'views' => 6890,
            'body' => [
                ['p' => 'Teams reach for a heavyweight orchestration platform the day they write their second script. Then the platform becomes the project, and nobody is analysing data anymore.'],
                ['h2' => 'Start with boring infrastructure'],
                ['p' => 'A versioned raw-data bucket, a cron-driven scheduler and SQL views are enough to power a shocking amount of real analysis. Name your files well — source, date, partition — and the pipeline almost documents itself.'],
                ['p' => 'The critical habit is immutability. Never overwrite raw data in place. Every transformation should read from a snapshot and write a new one. That one rule saves you the moment a stakeholder asks "why do the numbers look different than last month?".'],
                ['blockquote' => 'A pipeline you can reproduce from raw data is a pipeline you can trust.'],
                ['p' => 'Add orchestration when you have many dependencies and real retries to manage — not before. Until then, the simplest thing that works is also the most maintainable thing that works.'],
            ],
        ],
        [
            'title' => 'Imposter Syndrome is Not a Personality Flaw',
            'excerpt' => 'New evidence reframes self-doubt as a context-driven response, and that reframing points to practical fixes.',
            'category' => 'science',
            'author' => 'daniel',
            'tags' => ['career-growth', 'wellbeing'],
            'views' => 10442,
            'body' => [
                ['p' => 'For decades imposter syndrome was described as an internal, fixed trait — a kind of personal defect that high achievers were told to "overcome". The research community has increasingly pushed back.'],
                ['h2' => 'It is a response to context'],
                ['p' => 'Studies find that feelings of fraud spike when people move into roles with less structure, less feedback and a steeper power gradient. The same person reports no imposter feelings in a role where expectations are clear and support is strong.'],
                ['p' => 'That makes self-doubt a signal about your environment, not your worth. When the feeling persists, the useful question is not "what is wrong with me?" but "what is this situation failing to provide?".'],
                ['blockquote' => 'Imposter syndrome is often a relationship problem between you and your context.'],
                ['p' => 'The interventions follow naturally: explicit expectations, early feedback, and a mentor who can calibrate what "good" actually looks like at your level. Fix the context, and the feeling loses most of its grip.'],
            ],
        ],
        [
            'title' => 'The Uncomfortable Truth About Free-To-Use Developer Tools',
            'excerpt' => 'If the tool is free, your data — or your attention — is the product. Understanding the economics keeps you in control.',
            'category' => 'technology',
            'author' => 'marco',
            'tags' => ['open-source', 'security', 'cloud-computing'],
            'views' => 15879,
            'body' => [
                ['p' => 'The phrase "free tier" is doing a lot of work. Free is a pricing decision, and pricing decisions exist to drive a business model somewhere. As a developer, understanding that model is part of evaluating the tool.'],
                ['h2' => 'Where the revenue comes from'],
                ['p' => 'Some free tools are genuinely loss-leaders for paid plans — that is the friendliest model. Others monetise your data for advertising or model training. Others monetise switching costs: the tool is free until leaving it is painful, then the bill arrives.'],
                ['p' => 'None of these are inherently evil. The problem is only when the incentives are hidden. An open-source core with a transparent company behind it is a very different risk profile from a closed black box with free unlimited everything.'],
                ['blockquote' => 'Read the license before you read the docs.'],
                ['p' => 'The practical habit is to ask one question before adopting a free tool: what makes this sustainable for the people who built it, and does that align with what I am trying to protect? The answer shapes almost every tooling decision you will make.'],
            ],
        ],
        [
            'title' => 'Remote Work is Not a Lifestyle. It Is a Management Discipline',
            'excerpt' => 'Async work fails when leaders run remote teams with in-office instincts. The fix is a written culture, not a video call marathon.',
            'category' => 'career',
            'author' => 'priya',
            'tags' => ['remote-work', 'focus'],
            'views' => 19350,
            'body' => [
                ['p' => 'The teams that thrive remotely are not the ones with the best video setups. They are the ones that replaced verbal coordination with written coordination and stopped pretending the hallway is a communication channel.'],
                ['h2' => 'Write it down'],
                ['p' => 'Every decision, every context, every rationale that would once have lived in a conversation now needs a written home. This is not bureaucracy. A document you can point to is how asynchronous teams avoid a second shift of hidden meetings held just to share context.'],
                ['p' => 'Leaders who fail at remote work usually fail here — they try to recreate the office on Zoom, then blame the format when attention fragments and calendars collapse into back-to-back calls.'],
                ['blockquote' => 'Remote work rewards writing. In writing, everyone hears the same sentence.'],
                ['p' => 'The measurement is simple: can a new hire who missed the last month of meetings fully catch up from the written record? If not, your communication lives in a channel that time erases.'],
            ],
        ],
        [
            'title' => 'A Practical Guide to Progressive Web Apps in 2026',
            'excerpt' => 'Service workers, offline shells and a few storage decisions turn a website into an app users keep on their home screen.',
            'category' => 'technology',
            'author' => 'sofia',
            'tags' => ['web-development', 'javascript'],
            'views' => 11209,
            'body' => [
                ['p' => 'Progressive web apps fell out of fashion for a while, then quietly became the default way to ship an "app" without the app store. The tooling caught up, and the results are genuinely good.'],
                ['h2' => 'The three pillars still matter'],
                ['p' => 'A PWA is three decisions: a service worker that controls the cache, a manifest that makes the app installable, and a shell that renders instantly from that cache. Get those right and everything else is a normal website with superpowers.'],
                ['p' => 'The storage strategy is where most apps go wrong. Cache-first for static assets, network-first for API data, and a sensible eviction policy for the rest. Users forgive a brief spinner; they do not forgive stale content shown as fresh.'],
                ['blockquote' => 'Offline support is not a feature. It is the difference between a site and an app.'],
                ['p' => 'Platform tools now validate your manifest and service worker at build time, so the setup is nearly frictionless. If your product is content-heavy and used repeatedly, a PWA is very likely the right default.'],
            ],
        ],
        [
            'title' => 'The 5-Hour Rule and What It Really Teaches About Learning',
            'excerpt' => 'Reading for an hour a day is the simplest high-leverage habit in existence — but only if you close the loop.',
            'category' => 'productivity',
            'author' => 'james',
            'tags' => ['focus', 'career-growth'],
            'views' => 27453,
            'body' => [
                ['p' => 'The 5-hour rule is disarmingly simple: spend at least one hour a day, five days a week, deliberately learning something new. No app required, no curriculum enforced, just protected time.'],
                ['h2' => 'Why it beats every productivity hack'],
                ['p' => 'Learning compounds the way savings do. An hour a day is roughly 250 hours a year — more than six working weeks of pure skill acquisition, applied to whatever you choose. Very few other investments return that much per hour of input.'],
                ['p' => 'But the loop only closes with output. Read, then summarise in your own words. Learn a technique, then ship it. The difference between passive consumption and deliberate practice is entirely in what you do with the hour afterwards.'],
                ['blockquote' => 'Input without output is entertainment. Output without input is repetition.'],
                ['p' => 'The practical version: keep a running list of questions you want to answer, and spend your hour answering one of them. The list makes the hour a search instead of a scroll, which is the entire game.'],
            ],
        ],
        [
            'title' => 'Typography is the Interface',
            'excerpt' => 'Before layout, before color, before any visual flourish, a design is a series of reading decisions.',
            'category' => 'design',
            'author' => 'aisha',
            'tags' => ['css', 'ux-research'],
            'views' => 7421,
            'body' => [
                ['p' => 'Remove every visual element from a page except the type and you still have a design. Remove the type and you have nothing. Type carries the interface, and its quality decides whether a product feels considered or cheap.'],
                ['h2' => 'Three decisions set the tone'],
                ['p' => 'Hierarchy — make the reading order obvious at a glance through scale, weight and spacing. Measure — keep lines between 45 and 75 characters for comfortable reading. Rhythm — give type a consistent vertical system so every page inherits the same breathing.'],
                ['p' => 'Accessibility closes the loop. Body text below 16px is a judgment call, and contrast ratios below 4.5:1 are a bug. A design that cannot be read comfortably is a design that does not work, regardless of how it renders in a screenshot.'],
                ['blockquote' => 'Good typography is invisible. You only notice it when it is missing.'],
                ['p' => 'Start your next design in grayscale and plain type. When the layout still reads clearly, add color and imagery knowing the information architecture is already solid. Type-first design is a discipline that shows.'],
            ],
        ],
        [
            'title' => 'How Machine Learning Actually Thinks (and What It Can\'t Do)',
            'excerpt' => 'A plain-language tour of what a model is, what training means, and the boundaries that still separate ML from general intelligence.',
            'category' => 'science',
            'author' => 'daniel',
            'tags' => ['machine-learning', 'artificial-intelligence', 'data-science'],
            'views' => 22180,
            'body' => [
                ['p' => 'Strip away the marketing and a machine learning model is a statistical pattern-matcher trained on examples. That sentence explains most of what AI can and cannot do, and almost nobody in the discourse starts from it.'],
                ['h2' => 'Training is pattern compression'],
                ['p' => 'A model does not store your data. It compresses the patterns in it into billions of weighted connections. Asking it a question is asking the compressed pattern to predict what an answer looks like — which is why it is so fluent and so capable of subtle error.'],
                ['p' => 'The consequences follow directly. It has no goals, no beliefs, no understanding of truth. It cannot reason about unseen situations the way a mind does. What it has is an extraordinarily broad, extraordinarily shallow statistical memory.'],
                ['blockquote' => 'A language model is the most confident student who ever mastered a test without understanding the subject.'],
                ['p' => 'This is not an argument against the technology — it is enormously useful. It is an argument about where to trust it. Drafting, summarising, finding patterns: yes. Facts, causality, judgment: verify every time.'],
            ],
        ],
        [
            'title' => 'The Email That Gets You a Response (and the One That Doesn\'t)',
            'excerpt' => 'Cold outreach fails for a handful of predictable reasons. Fix the subject line and the first sentence, and the reply rate follows.',
            'category' => 'career',
            'author' => 'priya',
            'tags' => ['writing', 'career-growth'],
            'views' => 18873,
            'body' => [
                ['p' => 'Most cold emails are ignored because they are asking for a favour from a stranger while offering nothing in return — not because the recipient is busy. The fix is a structural one.'],
                ['h2' => 'Lead with what you can give'],
                ['p' => 'The highest-performing outreach I have seen starts with a specific, useful offer: a relevant observation, a connection, a piece of feedback. It asks for the smallest possible response — a yes/no — and makes refusing feel cheap.'],
                ['p' => 'Specificity is the trust engine. A generic compliment reads as template spam. A sentence that shows you read their actual work, and cites it, converts at multiples. Attention is the currency, and specificity is proof you spent it.'],
                ['blockquote' => 'If your email could have been sent to anyone, it will be answered by no one.'],
                ['p' => 'One structural note: keep it under 120 words and put the ask in the second half. If the recipient reads the whole thing, they have already done the work of considering you. Respect that effort with brevity.'],
            ],
        ],
        [
            'title' => 'What I Learned Debugging a Production Outage for 14 Hours',
            'excerpt' => 'The expensive lesson was not technical. It was about how teams communicate when the pressure is on.',
            'category' => 'technology',
            'author' => 'sofia',
            'tags' => ['devops', 'security', 'databases'],
            'views' => 15204,
            'body' => [
                ['p' => 'The incident started at 2 a.m. with a burst of alerts and ended with a fix so small it felt embarrassing. In between were fourteen hours of coordinated chaos, and most of the damage was self-inflicted.'],
                ['h2' => 'The bug was a cache stampede'],
                ['p' => 'A cold-cache restart had a single hot key, thousands of processes re-computing the same expensive query at once, and the database buckling under the retry storm. The fix was a lock and a longer TTL. It took ninety seconds to deploy.'],
                ['p' => 'Why did it take fourteen hours? Because half the team was investigating the wrong layer, status updates were duplicated in three channels, and nobody declared what was out of scope until the third hour. The coordination cost, not the debugging, ate the night.'],
                ['blockquote' => 'In an incident, the first thing to fix is the conversation.'],
                ['p' => 'What we changed afterwards: one shared status channel with a single owner, a pre-written runbook for cache layer symptoms, and a rule that any fix is applied to a staging reproduction before production. The next outage — there is always a next one — cost two hours.'],
            ],
        ],
        [
            'title' => 'The Case for Taking an Actual Lunch Break',
            'excerpt' => 'Skipping lunch to feel productive is a productivity error, and the neuroscience explains why.',
            'category' => 'wellbeing',
            'author' => 'james',
            'tags' => ['wellbeing', 'focus', 'remote-work'],
            'views' => 16420,
            'body' => [
                ['p' => 'Eating lunch at your desk while answering email feels like efficiency. The research keeps showing it is the opposite: the least efficient version of every activity you are trying to combine.'],
                ['h2' => 'Attention is a finite resource'],
                ['p' => 'Your brain depletes its ability to maintain focus over the course of a morning, and a real break — away from screens, preferably with movement — is how it replenishes. Working through lunch borrows concentration from the afternoon and charges interest.'],
                ['p' => 'There is also a decision-quality angle. Blood glucose dips and decision fatigue combine around 2 p.m., which is precisely when the email-while-eating crowd makes its worst calls. A proper meal is a risk-management tool.'],
                ['blockquote' => 'The shortest path to a better afternoon is a lunch break that looks like one.'],
                ['p' => 'The practice is unglamorous: leave your desk, eat something real, do not look at a screen. The people who do this reliably report afternoons that are measurably more productive than the lunch-skippers — which is to say, the evidence matches the intuition.'],
            ],
        ],
        [
            'title' => 'Rethinking the Dashboard: Less Is More, Always',
            'excerpt' => 'Dashboards fail when they show everything. The best ones are opinionated about what matters right now.',
            'category' => 'design',
            'author' => 'aisha',
            'tags' => ['ux-research', 'data-science'],
            'views' => 6872,
            'body' => [
                ['p' => 'Every dashboard I have audited started life as "show all the data we have" and ended as a wall of numbers nobody can act on. The cure is editorial discipline: dashboards should make decisions faster, not show more.'],
                ['h2' => 'One screen, one question'],
                ['p' => 'A great dashboard is built around a single decision it exists to accelerate. Revenue dashboards answer "is this week on track?". Marketing dashboards answer "where should the next dollar go?". If you cannot name the question, you cannot design the screen.'],
                ['p' => 'The layout follows from the question. The answer lives at the top in one number. The drivers live below it. Everything else is drill-down — a click away, never a fixture on the page.'],
                ['blockquote' => 'The best dashboard is the one that lets you see the answer without asking anyone.'],
                ['p' => 'The test is brutal and useful: put the dashboard in front of its intended user and ask them to make one real decision from it in thirty seconds. If they cannot, the dashboard is decoration.'],
            ],
        ],
        [
            'title' => 'Why Your Side Project Keeps Failing (and What to Do About It)',
            'excerpt' => 'Side projects die from ambition, not laziness. Scope is the only thing that matters.',
            'category' => 'productivity',
            'author' => 'marco',
            'tags' => ['startups', 'focus', 'web-development'],
            'views' => 24812,
            'body' => [
                ['p' => 'I have started, and abandoned, more side projects than I care to count. The pattern is always the same: a big idea, a busy month, and a graveyard of half-built infrastructure.'],
                ['h2' => 'Shrink the definition of done'],
                ['p' => 'A side project does not need to be a business. It needs to be a thing that exists and works, however small. The version of your idea that ships in four weekends of spare time is the version that has any chance of surviving at all.'],
                ['p' => 'Cut ruthlessly against the boring parts. You do not need auth on day one. You do not need a payment system. You need the one feature that makes your idea feel real to a stranger, and everything else is scope creep in disguise.'],
                ['blockquote' => 'The enemy of the side project is the roadmap.'],
                ['p' => 'And when you ship, celebrate it. A finished small thing compounds your confidence for the next one; an unfinished grand thing teaches you nothing except how to abandon things. Ship small, ship often, ship ugly.'],
            ],
        ],
    ];

    /**
     * Seed the application's posts.
     */
    public function run(): void
    {
        $publishedAt = now()->subDays(120);

        foreach (self::POSTS as $index => $definition) {
            $author = User::where('username', $definition['author'])->first();
            $category = Category::where('slug', $definition['category'])->first();

            if (! $author || ! $category) {
                continue;
            }

            $content = collect($definition['body'])
                ->map(fn (array $block) => match (array_key_first($block)) {
                    'h2' => '<h2>'.reset($block).'</h2>',
                    'blockquote' => '<blockquote><p>'.reset($block).'</p></blockquote>',
                    default => '<p>'.reset($block).'</p>',
                })
                ->implode("\n");

            $post = Post::firstOrCreate(
                ['slug' => Str::slug($definition['title'])],
                [
                    'uuid' => Str::uuid(),
                    'title' => $definition['title'],
                    'excerpt' => $definition['excerpt'],
                    'content' => $content,
                    'featured_image' => null,
                    'thumbnail' => null,
                    'author_id' => $author->id,
                    'category_id' => $category->id,
                    'status' => PostStatus::Published->value,
                    'visibility' => PostVisibility::Public->value,
                    'published_at' => $publishedAt->addDays(rand(1, 5))->addHours(rand(6, 20)),
                    'scheduled_at' => null,
                    'reading_time' => max(1, round(str_word_count(strip_tags($content)) / 200)),
                    'view_count' => $definition['views'] + $index,
                    'like_count' => rand(20, 400),
                    'comment_count' => rand(2, 60),
                ]
            );

            $post->tags()->sync(
                Tag::whereIn('slug', collect($definition['tags'])->map(fn (string $tag) => Str::slug($tag)))->pluck('id')
            );
        }

        if (Post::where('status', PostStatus::Draft->value)->doesntExist()) {
            Post::factory()
                ->count(2)
                ->draft()
                ->create();
        }

        if (Post::where('status', PostStatus::Pending->value)->doesntExist()) {
            Post::factory()
                ->count(2)
                ->pending()
                ->create();
        }
    }
}
