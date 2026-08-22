<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSMS — Course Syllabus Management System | CLSU</title>
    <meta name="description" content="Central Luzon State University's official platform for creating, reviewing, and approving course syllabi across all colleges and departments.">

    <!-- Preload LCP image with high priority -->
    <link rel="preload" href="{{ asset('assets/CLSU-Siever.webp') }}" as="image" fetchpriority="high">

    @vite(['resources/css/landing.css'])
    
</head>
<body class="font-sans text-ink antialiased" style="
    --bg-paper: url('{{ asset('assets/CLSU-Siever.webp') }}');
    --bg-forest: url('{{ asset('assets/CLSU-Siever-1.webp') }}');
    --bg-ink: url('{{ asset('assets/CLSU-Siever-2.webp') }}');
">

    {{-- NAV --}}
    <nav class="fixed top-0 inset-x-0 z-50 bg-paper/90 backdrop-blur-md border-b border-ink/10">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <a href="#top" class="flex items-center gap-3 shrink-0">
                <img src="/assets/CLSU-LOGO-removebg.png" alt="CLSU official seal" class="h-8 w-8 object-contain">
                <span class="ledger-head text-forest text-lg tracking-tight">CSMS</span>
            </a>

            <div class="hidden md:flex items-center gap-8 text-sm font-medium text-slate">
                <a href="#workflow" class="tab-link hover:text-ink">How it works</a>
                <a href="#roles" class="tab-link hover:text-ink">Who it's for</a>
                <a href="#access" class="tab-link hover:text-ink">Access &amp; security</a>
            </div>

            <a href="{{ route('auth.login') }}"
               class="btn-flat inline-flex items-center gap-2 font-semibold text-sm px-5 py-2.5">
                Sign in
                <i class="bx bx-right-arrow-alt text-base" aria-hidden="true"></i>
            </a>
        </div>
    </nav>

    {{-- HERO --}}
    <section id="top" style="background-position: top;" class="grain photo-bg photo-bg-paper relative bg-cover bg-no-repeat pt-32 pb-20 md:pt-40 md:pb-28 overflow-hidden">
        <div class="max-w-6xl mx-auto px-6 grid lg:grid-cols-[1.05fr_0.95fr] gap-16 items-center">

            <div>
                <p class="eyebrow text-xs text-gold-ink mb-5">Central Luzon State University</p>
                <h1 class="ledger-head text-ink text-[2.5rem] leading-[1.08] md:text-[3.6rem] md:leading-[1.02] mb-6">
                    COURSE SYLLABUS<br>
                    MANAGEMENT<br>
                    <span class="text-moss">SYSTEM</span>
                </h1>
                <p class="text-slate text-lg leading-relaxed mb-8 max-w-md">
                    One record for every syllabus at CLSU — drafted, reviewed, and approved in the same place, with a clear trail of who did what and when.
                </p>

                <div class="flex flex-wrap items-center gap-4">
                    <a href="{{ route('auth.login') }}"
                       class="btn-flat inline-flex items-center gap-2 font-semibold px-6 py-3.5 text-base">
                        Sign in to CSMS
                    </a>
                    <a href="#workflow" class="inline-flex items-center gap-2 text-ink font-semibold px-2 py-3.5 text-base tab-link">
                        See how approval works
                        <i class="bx bx-down-arrow-alt" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

            {{-- document stack visual — layered card conveys "one record" over "scattered files" --}}
            <div class="relative h-[420px] hidden sm:block" aria-hidden="true">
                <div class="absolute inset-x-6 top-6 h-[300px] rounded-[var(--radius-xl)] bg-forest/[0.06] border border-forest/10"></div>
                <div class="doc-card ruled-line absolute inset-x-0 top-0 h-[320px] p-6 flex flex-col">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <p class="eyebrow text-[10px] text-slate mb-1">Course syllabus</p>
                            <p class="ledger-head text-ink text-lg">COURSE 101 — Course Title</p>
                        </div>
                        <span class="stamp stamp-review rotate-[-6deg]">Under review</span>
                    </div>
                    <div class="space-y-2 flex-1">
                        <div class="h-2 rounded-full bg-ink/10 w-[85%]"></div>
                        <div class="h-2 rounded-full bg-ink/10 w-[70%]"></div>
                        <div class="h-2 rounded-full bg-ink/10 w-[92%]"></div>
                        <div class="h-2 rounded-full bg-ink/10 w-[60%]"></div>
                    </div>
                    <div class="flex items-center gap-2 pt-4 border-t border-ink/10 mt-2">
                        <div class="w-6 h-6 rounded-full bg-forest/15 flex items-center justify-center">
                            <i class="bx bx-user text-forest text-xs" aria-hidden="true"></i>
                        </div>
                        <p class="text-xs text-slate">Routed to Dept. Chair, then College Dean</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- HOW IT WORKS — the single source of truth for the workflow, no separate "what it does" restating it --}}
    <section id="workflow" class="photo-bg photo-bg-forest py-24 text-paper">
        <div class="max-w-6xl mx-auto px-6">
            <div class="max-w-xl mb-20">
                <p class="eyebrow text-xs text-gold/80 mb-3">How it works</p>
                <h2 class="ledger-head text-3xl md:text-4xl leading-tight mb-4">Same route, every time.</h2>
                <p class="text-paper/60 leading-relaxed">
                    Every syllabus follows the same four-stage path from account to approval — no shortcuts, and no ambiguity about where it stands.
                </p>
            </div>

            <div class="grid md:grid-cols-4 gap-x-6 gap-y-12">
                @foreach([
                    ['bx-user-plus', 'Register', 'Sign up with your CLSU email. An admin confirms your account before you can log in.'],
                    ['bx-edit', 'Draft', 'Complete the syllabus wizard — outcomes, weekly coverage, and grading weights all have to be filled in before you can submit.'],
                    ['bx-send', 'Chair review', 'Your department chair reviews the submission, and can return it with feedback or endorse it to the dean.'],
                    ['bx-check-double', 'Dean approval', 'The dean gives final sign-off. The syllabus is then locked, stored, and available for download.'],
                ] as $i => $step)
                <div class="relative border-l-2 border-paper/15 pl-5">
                    <span class="index-tab tabular text-gold block mb-3">0{{ $i + 1 }}</span>
                    <div class="w-10 h-10 rounded-[var(--radius-md)] bg-paper/10 border border-paper/25 flex items-center justify-center mb-4">
                        <i class="bx {{ $step[0] }} text-lg text-gold" aria-hidden="true"></i>
                    </div>
                    <h4 class="ledger-head text-lg mb-2">{{ $step[1] }}</h4>
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
                <p class="eyebrow text-xs text-gold-ink mb-3">Who it's for</p>
                <h2 class="ledger-head text-ink text-3xl md:text-4xl leading-tight mb-4">Each role sees only what it needs.</h2>
                <p class="text-slate leading-relaxed">Access is scoped by role — no one sees or touches what isn't theirs to manage.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach([
                    ['bx-shield', 'Admin', 'Full system access — manages accounts, approves registrations, and maintains the college, department, and calendar structure.'],
                    ['bx-building', 'Dean', 'Scoped to their college — sets college goals and gives final approval on syllabi submitted within it.'],
                    ['bx-group', 'Chair', 'Scoped to their department — manages objectives, POs, and courses, and reviews faculty syllabi before they reach the dean.'],
                    ['bx-book-open', 'Faculty', 'Creates and manages their own syllabi. Any faculty can draft a syllabus for any course — creation isn\'t restricted by department.'],
                ] as $role)
                <div class="feature-card p-6">
                    <i class="bx {{ $role[0] }} text-2xl text-forest mb-4 block" aria-hidden="true"></i>
                    <h4 class="ledger-head text-ink text-lg mb-2">{{ $role[1] }}</h4>
                    <p class="text-slate text-sm leading-relaxed">{{ $role[2] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ACCESS & SECURITY — consolidated from three overlapping points to two distinct ones --}}
    <section id="access" class="py-20 bg-white border-y border-ink/10">
        <div class="max-w-6xl mx-auto px-6 grid md:grid-cols-2 gap-8 max-w-4xl">
            <div class="flex gap-4">
                <div class="w-10 h-10 rounded-[var(--radius-md)] bg-forest/10 flex items-center justify-center shrink-0">
                    <i class="bx bx-envelope text-lg text-forest" aria-hidden="true"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-ink mb-1">Institutional email only</h4>
                    <p class="text-slate text-sm leading-relaxed">Registration is restricted to @clsu.edu.ph and @clsu2.edu.ph addresses — other domains are rejected outright.</p>
                </div>
            </div>
            <div class="flex gap-4">
                <div class="w-10 h-10 rounded-[var(--radius-md)] bg-forest/10 flex items-center justify-center shrink-0">
                    <i class="bx bx-user-check text-lg text-forest" aria-hidden="true"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-ink mb-1">Manually approved</h4>
                    <p class="text-slate text-sm leading-relaxed">A verified email proves ownership, not faculty status — an admin reviews and approves every account before it gets access.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- FINAL CTA --}}
    <section class="grain photo-bg photo-bg-ink relative py-24 text-paper overflow-hidden">
        <div class="max-w-2xl mx-auto px-6 text-center relative">
            <span class="stamp stamp-approved bg-paper/95 mb-6 inline-flex">
                <i class="bx bx-check" aria-hidden="true"></i>Ready when you are
            </span>
            <h2 class="ledger-head text-3xl md:text-4xl mb-4">Sign in with your CLSU email.</h2>
            <p class="text-paper/70 mb-9 leading-relaxed max-w-md mx-auto">
                New to CSMS? Registration is on the same page — just sign up and wait for admin approval.
            </p>
            <a href="{{ route('auth.login') }}"
               class="inline-flex items-center gap-2 bg-gold text-ink font-semibold px-8 py-4 text-base rounded-[var(--radius-sm)] shadow-lg hover:bg-paper transition-colors">
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
                    <img src="/assets/CLSU-LOGO-removebg.webp" alt="CLSU official seal" class="h-9 w-9 object-contain mt-0.5">
                    <div>
                        <p class="ledger-head text-paper text-base leading-tight">Course Syllabus<br>Management System</p>
                        <p class="text-paper/50 text-xs mt-2">Central Luzon State University<br>Science City of Muñoz, Nueva Ecija</p>
                    </div>
                </div>
                <nav class="flex gap-8 text-sm" aria-label="Footer">
                    <a href="#workflow" class="tab-link hover:text-paper">How it works</a>
                    <a href="#roles" class="tab-link hover:text-paper">Who it's for</a>
                    <a href="#access" class="tab-link hover:text-paper">Access &amp; security</a>
                    <a href="{{ route('auth.login') }}" class="tab-link hover:text-paper">Sign in</a>
                </nav>
            </div>
            <div class="pt-6 border-t border-paper/10 flex flex-col sm:flex-row justify-between gap-2 text-xs text-paper/40">
                <p>&copy; {{ date('Y') }} Central Luzon State University. Internal use only.</p>
                <p>Restricted to @clsu.edu.ph and @clsu2.edu.ph accounts.</p>
            </div>
        </div>
    </footer>

    <script>
        if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            document.querySelectorAll('.doc-card, .feature-card, section h2').forEach(el => el.classList.add('reveal'));
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