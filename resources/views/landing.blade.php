<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSMS — Course Syllabus Management System | CLSU</title>
    <meta name="description" content="Central Luzon State University's official platform for creating, reviewing, and approving course syllabi across all colleges and departments.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ink:      '#001a08',
                        forest:   '#002a0c',
                        moss:     '#004d16',
                        parchment:'#eef1ee',
                        paper:    '#f4f6f4',
                        gold:     '#c9a227',
                        'gold-ink':'#9a7a10',
                        slate:    '#3a4d3e',
                    },
                    fontFamily: {
                        display: ['"Fraunces"', 'serif'],
                        sans: ['"IBM Plex Sans"', 'system-ui', 'sans-serif'],
                        mono: ['"IBM Plex Mono"', 'monospace'],
                    }
                }
            }
        }
    </script>
    <style>
        html { scroll-behavior: smooth; }
        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            *, *::before, *::after { animation-duration: 0.001ms !important; animation-iteration-count: 1 !important; transition-duration: 0.001ms !important; }
        }

        body { background: #f4f6f4; }

        .grain {
            position: relative;
        }
        .grain::before {
            content: '';
            position: absolute; inset: 0;
            pointer-events: none;
            opacity: 0.04;
            mix-blend-mode: multiply;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
        }

        .stamp {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-family: 'IBM Plex Mono', monospace;
            font-weight: 600;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            border: 1.5px solid currentColor;
            border-radius: 999px;
            padding: 0.35rem 0.85rem;
            position: relative;
        }
        .stamp::after {
            content: '';
            position: absolute; inset: -3px;
            border: 1px solid currentColor;
            border-radius: 999px;
            opacity: 0.35;
        }
        .stamp-approved { color: #004d16; }
        .stamp-review   { color: #9a7a10; }
        .stamp-draft    { color: #5b5b52; }

        .doc-card {
            background: #ffffff;
            border: 1px solid rgba(0,42,12,0.11);
            box-shadow: 0 1px 0 rgba(0,42,12,0.04), 0 18px 40px -20px rgba(0,42,12,0.22);
        }

        .ruled-line {
            background-image: repeating-linear-gradient(to bottom, transparent, transparent 27px, rgba(0,42,12,0.07) 28px);
        }

        .link-underline {
            position: relative;
        }
        .link-underline::after {
            content: '';
            position: absolute; left: 0; right: 100%; bottom: -3px;
            height: 1.5px; background: currentColor;
            transition: right 0.25s ease;
        }
        .link-underline:hover::after { right: 0; }

        a:focus-visible, button:focus-visible {
            outline: 2.5px solid #c9a227;
            outline-offset: 3px;
            border-radius: 4px;
        }

        .reveal {
            opacity: 0;
            transform: translateY(14px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        .reveal.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        .step-thread {
            background-image: repeating-linear-gradient(to right, rgba(255,255,255,0.25) 0, rgba(255,255,255,0.25) 6px, transparent 6px, transparent 14px);
            height: 2px;
        }
    </style>
</head>
<body class="font-sans text-ink antialiased">

    {{-- NAV --}}
    <nav class="fixed top-0 inset-x-0 z-50 bg-paper/90 backdrop-blur-md border-b border-ink/10">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <a href="#top" class="flex items-center gap-3 shrink-0">
                <img src="/assets/clsu-logo-green.png" alt="CLSU official seal" class="h-8 w-8 object-contain">
                <span class="font-display font-semibold text-forest text-lg tracking-tight">CSMS</span>
            </a>

            <div class="hidden md:flex items-center gap-8 text-sm font-medium text-slate">
                <a href="#about" class="link-underline hover:text-ink">What it does</a>
                <a href="#workflow" class="link-underline hover:text-ink">How it works</a>
                <a href="#roles" class="link-underline hover:text-ink">Who it's for</a>
            </div>

            <a href="{{ route('auth.show') }}"
               class="inline-flex items-center gap-2 bg-forest text-paper font-semibold text-sm px-5 py-2.5 rounded-md hover:bg-ink transition-colors">
                Sign in
                <i class="bx bx-right-arrow-alt text-base" aria-hidden="true"></i>
            </a>
        </div>
    </nav>

    {{-- HERO --}}
    <section id="top" class="grain relative pt-32 pb-20 md:pt-40 md:pb-28 overflow-hidden bg-white">
        <div class="max-w-6xl mx-auto px-6 grid lg:grid-cols-[1.05fr_0.95fr] gap-16 items-center">

            <div>
                <p class="font-mono text-xs tracking-[0.2em] uppercase text-gold-ink mb-5">
                    Central Luzon State University
                </p>
                <h1 class="font-display font-semibold text-ink text-[2.6rem] leading-[1.08] md:text-6xl md:leading-[1.05] mb-6">
                    Course Syllabus<br>
                    Management<br>
                    <span class="italic text-moss">System</span>
                </h1>
                <p class="text-slate text-lg leading-relaxed mb-8 max-w-md">
                    CSMS is CLSU's official platform for drafting, reviewing, and approving course syllabi.
                </p>

                <div class="flex flex-wrap items-center gap-4 mb-10">
                    <a href="{{ route('auth.show') }}"
                       class="inline-flex items-center gap-2 bg-forest text-paper font-semibold px-6 py-3.5 rounded-md hover:bg-ink transition-colors text-base shadow-lg shadow-forest/20">
                        Sign in to CSMS
                    </a>
                    <a href="#workflow"
                       class="inline-flex items-center gap-2 text-ink font-semibold px-2 py-3.5 text-base link-underline">
                        See how approval works
                        <i class="bx bx-down-arrow-alt" aria-hidden="true"></i>
                    </a>
                </div>

                {{-- <div class="flex flex-wrap gap-3">
                    <span class="stamp stamp-approved"><i class="bx bx-lock-alt" aria-hidden="true"></i>OTP-verified</span>
                    <span class="stamp stamp-review"><i class="bx bx-git-branch" aria-hidden="true"></i>Structured review</span>
                    <span class="stamp stamp-draft"><i class="bx bx-buildings" aria-hidden="true"></i>All colleges</span>
                </div> --}}
            </div>

            {{-- document stack visual --}}
            <div class="relative h-[420px] hidden sm:block" aria-hidden="true">
                {{-- <div class="doc-card ruled-line absolute inset-x-6 top-10 h-[320px] rounded-sm rotate-[4deg] opacity-60"></div>
                <div class="doc-card ruled-line absolute inset-x-3 top-5 h-[320px] rounded-sm rotate-[-2deg] opacity-85"></div> --}}
                <div class="doc-card ruled-line absolute inset-x-0 top-0 h-[320px] rounded-sm p-6 flex flex-col">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <p class="font-mono text-[10px] tracking-widest uppercase text-slate mb-1">Course syllabus</p>
                            <p class="font-display font-semibold text-ink text-lg">COURSE 101 — Course Title</p>
                        </div>
                        <span class="stamp stamp-review rotate-[-8deg] text-[10px] px-2.5 py-1">Under review</span>
                    </div>
                    <div class="space-y-2 flex-1">
                        <div class="h-2 rounded bg-ink/10 w-[85%]"></div>
                        <div class="h-2 rounded bg-ink/10 w-[70%]"></div>
                        <div class="h-2 rounded bg-ink/10 w-[92%]"></div>
                        <div class="h-2 rounded bg-ink/10 w-[60%]"></div>
                    </div>
                    <div class="flex items-center gap-2 pt-4 border-t border-ink/10 mt-2">
                        <div class="w-6 h-6 rounded-full bg-forest/15 flex items-center justify-center">
                            <i class="bx bx-user text-forest text-xs" aria-hidden="true"></i>
                        </div>
                        <p class="text-xs text-slate">Routed to Dept. Chair, College Dean</p>
                    </div>
                </div>
                {{-- <span class="stamp stamp-approved absolute -bottom-2 -left-2 rotate-[-10deg] bg-paper text-xs shadow-md">
                    <i class="bx bx-check" aria-hidden="true"></i>Approved · Sample
                </span> --}}
            </div>
        </div>
    </section>

    {{-- WHAT IT DOES --}}
    <section id="about" class="py-24 bg-white border-t border-ink/10">
        <div class="max-w-6xl mx-auto px-6">
            <div class="max-w-xl mb-16">
                <p class="font-mono text-xs tracking-[0.2em] uppercase text-gold-ink mb-3">What it does</p>
                <h2 class="font-display font-semibold text-ink text-3xl md:text-4xl leading-tight mb-4">
                    Draft. Review. Approve.
                </h2>
                <p class="text-slate leading-relaxed">
                    CSMS replaces scattered documents and email threads with a single traceable record — who wrote it, who reviewed it, and when it was approved.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                @foreach([
                    ['bx-edit-alt', 'Build the syllabus', 'Faculty complete a step-by-step wizard covering course outcomes, weekly coverage, and grading weights. The system enforces completeness — a syllabus cannot be submitted until all required sections are filled.'],
                    ['bx-message-detail', 'Chair review', 'Submitted syllabi go to the department chair first. The chair can return it with feedback or endorse it to the dean — keeping incomplete work from reaching final approval.'],
                    ['bx-badge-check', 'Dean approval', 'The dean gives final sign-off for the college. Once approved, the syllabus is locked and stored — no more confusion over which version was the final one.'],
                ] as $i => $f)
                <div class="doc-card rounded-lg p-7">
                    <div class="w-11 h-11 rounded-md bg-forest/10 flex items-center justify-center mb-6">
                        <i class="bx {{ $f[0] }} text-xl text-forest" aria-hidden="true"></i>
                    </div>
                    <h3 class="font-display font-semibold text-ink text-xl mb-2.5">{{ $f[1] }}</h3>
                    <p class="text-slate text-[0.95rem] leading-relaxed">{{ $f[2] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- HOW IT WORKS --}}
    <section id="workflow" class="py-24 bg-forest text-paper">
        <div class="max-w-6xl mx-auto px-6">
            <div class="max-w-xl mb-20">
                <p class="font-mono text-xs tracking-[0.2em] uppercase text-gold/80 mb-3">How it works</p>
                <h2 class="font-display font-semibold text-3xl md:text-4xl leading-tight mb-4">
                    Same route, every time.
                </h2>
                <p class="text-paper/60 leading-relaxed">
                    Every syllabus follows the same four-step path — no shortcuts, no ambiguity about where it stands.
                </p>
            </div>

            <div class="grid md:grid-cols-4 gap-x-6 gap-y-12 relative">
                @foreach([
                    ['bx-user-plus', 'Register', 'Sign up with your CLSU email. Verify via OTP, then wait for an admin to confirm your account before you can log in.', 'draft'],
                    ['bx-edit', 'Draft', 'Complete the syllabus wizard — calendar, course components, outcomes, weekly coverage, and grading weights must all be filled before you can submit.', 'draft'],
                    ['bx-send', 'Chair review', 'The department chair reviews the submission. They can return it with feedback or endorse it to the dean.', 'review'],
                    ['bx-check-double', 'Dean approval', 'The dean gives final approval. The syllabus is then locked, stored, and available for download.', 'approved'],
                ] as $i => $step)
                <div class="relative">
                    {{-- @if($i < 3)
                    <div class="step-thread hidden md:block absolute top-6 left-[calc(50%+28px)] right-[calc(-50%+28px)]"></div>
                    @endif --}}
                    <div class="relative z-10 w-12 h-12 rounded-full bg-paper/10 border border-paper/25 flex items-center justify-center mb-5">
                        <i class="bx {{ $step[0] }} text-xl text-gold" aria-hidden="true"></i>
                    </div>
                    <span class="stamp stamp-{{ $step[3] }} !text-[10px] mb-3 bg-paper/95">{{ $step[3] === 'draft' ? 'Stage ' . ($i+1) : ($step[3] === 'review' ? 'Stage 3' : 'Stage 4') }}</span>
                    <h4 class="font-display font-semibold text-lg mb-2">{{ $step[1] }}</h4>
                    <p class="text-paper/60 text-sm leading-relaxed">{{ $step[2] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ROLES --}}
    <section id="roles" class="py-24 bg-parchment">
        <div class="max-w-6xl mx-auto px-6">
            <div class="max-w-xl mb-16">
                <p class="font-mono text-xs tracking-[0.2em] uppercase text-gold-ink mb-3">Who it's for</p>
                <h2 class="font-display font-semibold text-ink text-3xl md:text-4xl leading-tight mb-4">
                    Each role sees only what it needs.
                </h2>
                <p class="text-slate leading-relaxed">
                    Access is scoped by role — no one sees or touches what isn't theirs to manage.
                </p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach([
                    ['bx-shield', 'Admin', 'Full system access — manages user accounts, approves registrations, maintains the college and department structure, and oversees the academic calendar.'],
                    ['bx-building', 'Dean', 'Scoped to their college — manages college goals and gives final approval on syllabi submitted within their college.'],
                    ['bx-group', 'Chair', 'Scoped to their department — manages objectives, PEOs, POs, and courses, and reviews faculty syllabi before they reach the dean.'],
                    ['bx-book-open', 'Faculty', 'Creates and manages their own syllabi. Any faculty can draft a syllabus for any course — there is no department restriction on creation.'],
                ] as $role)
                <div class="bg-white border border-ink/10 rounded-lg p-6 hover:-translate-y-1 hover:shadow-lg hover:shadow-ink/5 transition-all duration-200">
                    <i class="bx {{ $role[0] }} text-2xl text-forest mb-4 block" aria-hidden="true"></i>
                    <h4 class="font-display font-semibold text-ink text-lg mb-2">{{ $role[1] }}</h4>
                    <p class="text-slate text-sm leading-relaxed">{{ $role[2] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- SECURITY / ACCESS --}}
    <section class="py-20 bg-white border-y border-ink/10">
        <div class="max-w-6xl mx-auto px-6 grid md:grid-cols-3 gap-8">
            @foreach([
                ['bx-envelope', 'Institutional email only', 'Registration is restricted to @clsu.edu.ph and @clsu2.edu.ph addresses. Other email domains are rejected outright.'],
                ['bx-mobile-alt', 'OTP verification', 'A 6-digit one-time code is sent to the registered email. The account is not verified until the correct code is entered.'],
                ['bx-user-check', 'Admin approval', 'Verifying an email proves ownership — not faculty status. An admin manually reviews and approves each account before access is granted.'],
            ] as $s)
            <div class="flex gap-4">
                <div class="w-10 h-10 rounded-full bg-forest/10 flex items-center justify-center shrink-0">
                    <i class="bx {{ $s[0] }} text-lg text-forest" aria-hidden="true"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-ink mb-1">{{ $s[1] }}</h4>
                    <p class="text-slate text-sm leading-relaxed">{{ $s[2] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    {{-- FINAL CTA --}}
    <section class="grain relative py-24 bg-ink text-paper overflow-hidden">
        <div class="max-w-2xl mx-auto px-6 text-center relative">
            <span class="stamp stamp-approved bg-paper/95 mb-6 inline-flex">
                <i class="bx bx-check" aria-hidden="true"></i>Ready when you are
            </span>
            <h2 class="font-display font-semibold text-3xl md:text-4xl mb-4">
                Sign in with your CLSU email.
            </h2>
            <p class="text-paper/70 mb-9 leading-relaxed max-w-md mx-auto">
                New to CSMS? Registration is on the same page — verify your email by OTP, and an admin will approve your account before you get access.
            </p>
            <a href="{{ route('auth.show') }}"
               class="inline-flex items-center gap-2 bg-gold text-ink font-semibold px-8 py-4 rounded-md hover:bg-paper transition-colors text-base shadow-lg">
                Go to sign-in
                <i class="bx bx-right-arrow-alt text-xl" aria-hidden="true"></i>
            </a>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="bg-forest text-paper/70 py-14">
        <div class="max-w-6xl mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between gap-10 mb-10">
                <div class="flex items-start gap-3 max-w-xs">
                    <img src="/assets/clsu-logo-green.png" alt="CLSU official seal" class="h-9 w-9 object-contain mt-0.5">
                    <div>
                        <p class="font-display font-semibold text-paper text-base leading-tight">Course Syllabus<br>Management System</p>
                        <p class="text-paper/50 text-xs mt-2">Central Luzon State University<br>Science City of Muñoz, Nueva Ecija</p>
                    </div>
                </div>
                <nav class="flex gap-8 text-sm" aria-label="Footer">
                    <a href="#about" class="link-underline hover:text-paper">What it does</a>
                    <a href="#workflow" class="link-underline hover:text-paper">How it works</a>
                    <a href="#roles" class="link-underline hover:text-paper">Who it's for</a>
                    <a href="{{ route('auth.show') }}" class="link-underline hover:text-paper">Sign in</a>
                </nav>
            </div>
            <div class="pt-6 border-t border-paper/10 flex flex-col sm:flex-row justify-between gap-2 text-xs text-paper/40">
                <p>&copy; {{ date('Y') }} Central Luzon State University. Internal use only.</p>
                <p>Restricted to @clsu.edu.ph and @clsu2.edu.ph accounts.</p>
            </div>
        </div>
    </footer>

    <script>
        // scroll-reveal for section headers/cards — respects reduced-motion via CSS above
        if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            document.querySelectorAll('.doc-card, section h2').forEach(el => el.classList.add('reveal'));
            const io = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        io.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.15 });
            document.querySelectorAll('.reveal').forEach(el => io.observe(el));
        }
    </script>

</body>
</html>
