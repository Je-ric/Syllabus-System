<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSMS — Course Syllabus Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Source+Sans+3:ital,wght@0,300;0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'clsu-green': '#002a0c',
                        'clsu-mid': '#004d16',
                        'clsu-light': '#009639',
                        'clsu-gold': '#ffd700',
                    },
                    fontFamily: {
                        sans: ['"Source Sans 3"', 'system-ui', 'sans-serif'],
                        display: ['Anton', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        html { scroll-behavior: smooth; }
        .hero-bg {
            background: linear-gradient(135deg, #002a0c 0%, #004d16 50%, #006b1e 100%);
        }
        .gold-accent { color: #ffd700; }
        .step-line::after {
            content: '';
            position: absolute;
            top: 2rem;
            left: calc(50% + 2rem);
            width: calc(100% - 4rem);
            height: 2px;
            background: linear-gradient(90deg, #ffd700, #009639);
        }
    </style>
</head>
<body class="font-sans bg-white text-gray-800">

    {{-- NAV --}}
    <nav class="fixed top-0 inset-x-0 z-50 bg-clsu-green/95 backdrop-blur border-b border-white/10">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="/assets/clsu-logo-green.png" alt="CLSU" class="h-9 w-9 object-contain brightness-200">
                <div>
                    <p class="font-display text-white text-sm leading-none tracking-widest">CSMS</p>
                    <p class="text-white/50 text-[10px] leading-none mt-0.5">Course Syllabus Management</p>
                </div>
            </div>
            <a href="{{ route('auth.show') }}"
               class="inline-flex items-center gap-2 bg-clsu-gold text-clsu-green font-bold text-sm px-5 py-2 rounded-lg hover:bg-yellow-300 transition-colors">
                <i class="bx bx-log-in text-base"></i>
                Login
            </a>
        </div>
    </nav>

    {{-- HERO --}}
    <section class="hero-bg min-h-screen flex items-center pt-16">
        <div class="max-w-6xl mx-auto px-6 py-24 grid lg:grid-cols-2 gap-16 items-center">
            <div>
                <p class="text-clsu-gold text-sm font-semibold tracking-widest uppercase mb-4">Central Luzon State University</p>
                <h1 class="font-display text-white text-5xl lg:text-6xl leading-tight mb-6">
                    Course Syllabus<br>
                    <span class="text-clsu-gold">Management</span><br>
                    System
                </h1>
                <p class="text-white/70 text-lg leading-relaxed mb-8 max-w-lg">
                    CLSU's official platform for creating, reviewing, and approving course syllabi across all colleges and departments — structured, transparent, and efficient.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('auth.show') }}"
                       class="inline-flex items-center gap-2 bg-clsu-gold text-clsu-green font-bold px-7 py-3 rounded-xl hover:bg-yellow-300 transition-colors text-base shadow-lg">
                        <i class="bx bx-log-in"></i>
                        Sign In to CSMS
                    </a>
                    <a href="#about"
                       class="inline-flex items-center gap-2 border border-white/30 text-white px-7 py-3 rounded-xl hover:bg-white/10 transition-colors text-base">
                        Learn More
                        <i class="bx bx-chevron-down"></i>
                    </a>
                </div>
            </div>
            <div class="hidden lg:grid grid-cols-2 gap-4">
                @foreach([
                    ['bx-file-blank', 'Syllabus Creation', 'Faculty create structured syllabi through a guided wizard.'],
                    ['bx-search-alt', 'Chair Review', 'Department chairs review and provide feedback.'],
                    ['bx-check-shield', 'Dean Approval', 'Deans oversee and approve finalized syllabi.'],
                    ['bx-buildings', 'Multi-College', 'Covers all colleges and departments in CLSU.'],
                ] as $card)
                <div class="bg-white/10 backdrop-blur border border-white/20 rounded-2xl p-5">
                    <i class="bx {{ $card[0] }} text-3xl text-clsu-gold mb-3 block"></i>
                    <p class="text-white font-semibold text-sm mb-1">{{ $card[1] }}</p>
                    <p class="text-white/60 text-xs leading-relaxed">{{ $card[2] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ABOUT --}}
    <section id="about" class="py-24 bg-gray-50">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-16">
                <p class="text-clsu-light text-sm font-semibold tracking-widest uppercase mb-2">About the System</p>
                <h2 class="font-display text-clsu-green text-4xl mb-4">What is CSMS?</h2>
                <p class="text-gray-500 max-w-2xl mx-auto leading-relaxed">
                    CSMS is CLSU's centralized platform for managing course syllabi across all academic units. It enforces a structured workflow ensuring quality, consistency, and compliance with accreditation standards.
                </p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                @foreach([
                    ['bx-user-check', 'Role-Based Access', 'clsu-green', 'Faculty, Chairs, Deans, and Admins each have clearly defined access and responsibilities within the system.'],
                    ['bx-git-branch', 'Structured Workflow', 'clsu-light', 'Syllabi follow a defined path: Draft → Under Review → Approved, ensuring accountability at every stage.'],
                    ['bx-shield-quarter', 'OTP-Verified Accounts', 'clsu-gold', 'Registration is restricted to @clsu.edu.ph emails with OTP verification and admin approval for security.'],
                ] as $f)
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                    <div class="w-12 h-12 rounded-xl bg-{{ $f[2] }}/10 flex items-center justify-center mb-5">
                        <i class="bx {{ $f[0] }} text-2xl text-{{ $f[2] }}"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800 text-lg mb-2">{{ $f[1] }}</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">{{ $f[2] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- HOW IT WORKS --}}
    <section class="py-24 bg-white">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-16">
                <p class="text-clsu-light text-sm font-semibold tracking-widest uppercase mb-2">Workflow</p>
                <h2 class="font-display text-clsu-green text-4xl mb-4">How It Works</h2>
                <p class="text-gray-500 max-w-xl mx-auto">From registration to approved syllabus — a clear, accountable process.</p>
            </div>
            <div class="grid md:grid-cols-4 gap-6">
                @foreach([
                    ['1', 'bx-user-plus', 'Register', 'Sign up with your CLSU email. Verify via OTP and await admin approval.'],
                    ['2', 'bx-edit', 'Create Syllabus', 'Use the step-by-step wizard to build your course syllabus with outcomes, schedule, and evaluation.'],
                    ['3', 'bx-revision', 'Submit for Review', 'Submit to your department chair for review and feedback.'],
                    ['4', 'bx-badge-check', 'Get Approved', 'Once approved, your syllabus is finalized and available for download.'],
                ] as $step)
                <div class="text-center">
                    <div class="w-16 h-16 rounded-full bg-clsu-green flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="bx {{ $step[1] }} text-2xl text-clsu-gold"></i>
                    </div>
                    <p class="text-xs font-bold text-clsu-gold tracking-widest mb-1">STEP {{ $step[0] }}</p>
                    <h4 class="font-semibold text-gray-800 mb-2">{{ $step[2] }}</h4>
                    <p class="text-gray-500 text-sm leading-relaxed">{{ $step[3] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ROLES --}}
    <section class="py-24 bg-clsu-green">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-16">
                <p class="text-clsu-gold text-sm font-semibold tracking-widest uppercase mb-2">Access Control</p>
                <h2 class="font-display text-white text-4xl mb-4">Who Uses CSMS?</h2>
                <p class="text-white/60 max-w-xl mx-auto">Each role has a defined scope and set of responsibilities within the system.</p>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach([
                    ['bx-shield', 'Admin', 'Full system access. Manages users, structure, calendars, and organizational hierarchy.'],
                    ['bx-building', 'Dean', 'Oversees college goals and monitors the organizational hierarchy of their college.'],
                    ['bx-group', 'Chair', 'Manages department objectives, PEOs, POs, and courses. Reviews faculty syllabi.'],
                    ['bx-book-open', 'Faculty', 'Creates and manages their own syllabi through the guided wizard.'],
                ] as $role)
                <div class="bg-white/10 border border-white/20 rounded-2xl p-6 hover:bg-white/15 transition-colors">
                    <i class="bx {{ $role[0] }} text-3xl text-clsu-gold mb-4 block"></i>
                    <h4 class="text-white font-semibold text-lg mb-2">{{ $role[1] }}</h4>
                    <p class="text-white/60 text-sm leading-relaxed">{{ $role[2] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-24 bg-gray-50">
        <div class="max-w-2xl mx-auto px-6 text-center">
            <img src="/assets/clsu-logo-green.png" alt="CLSU" class="h-16 w-16 object-contain mx-auto mb-6">
            <h2 class="font-display text-clsu-green text-4xl mb-4">Ready to get started?</h2>
            <p class="text-gray-500 mb-8 leading-relaxed">
                Access CSMS using your official CLSU email address. New users must register and await admin approval before accessing the system.
            </p>
            <a href="{{ route('auth.show') }}"
               class="inline-flex items-center gap-2 bg-clsu-green text-white font-bold px-8 py-4 rounded-xl hover:bg-clsu-mid transition-colors text-base shadow-lg">
                <i class="bx bx-log-in text-xl"></i>
                Go to Login
            </a>
            <p class="text-gray-400 text-xs mt-4">Restricted to @clsu.edu.ph and @clsu2.edu.ph accounts only.</p>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="bg-clsu-green border-t border-white/10 py-8">
        <div class="max-w-6xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <img src="/assets/clsu-logo-green.png" alt="CLSU" class="h-7 w-7 object-contain brightness-200">
                <p class="text-white/60 text-sm">Central Luzon State University — CSMS</p>
            </div>
            <p class="text-white/30 text-xs">Course Syllabus Management System &copy; {{ date('Y') }}</p>
        </div>
    </footer>

</body>
</html>
