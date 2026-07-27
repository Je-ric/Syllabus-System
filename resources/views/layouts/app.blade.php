<!DOCTYPE html>
<html lang="en">
<head>
    @include('includes.head-assets')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600;700&display=swap" rel="stylesheet">
</head>

<body class="min-h-screen">

    @php
        $isWizardRoute = request()->routeIs('syllabus.wizard');
        $user = Auth::user();
    @endphp

    @if (session('toast'))
        <x-feedback-status.toast :message="session('toast')['message']" :type="session('toast')['type']" />
    @endif
    <x-feedback-status.toast />

    {{-- Mobile overlay --}}
    <div id="sidebar-overlay"
        class="fixed inset-0 bg-[#09090b]/40 backdrop-blur-sm z-30 hidden lg:hidden
               {{ $isWizardRoute ? 'hidden!' : '' }}">
    </div>

    {{-- ── Sidebar ──────────────────────────────────────────────────── --}}
    <aside id="app-sidebar"
        class="fixed inset-y-0 left-0 z-40 w-60
                bg-white
                flex flex-col
               border-r border-[#E3E8EB]
               transform -translate-x-full lg:translate-x-0
               transition-transform duration-300 ease-out
               {{ $isWizardRoute ? 'hidden lg:hidden' : '' }}"
        style="box-shadow: 1px 0 0 rgba(16,24,40,0.02), 4px 0 24px -8px rgba(16,24,40,0.06);">

        {{-- Brand header --}}
        <div class="shrink-0 px-4 pt-5 pb-4 border-b border-[#F1F3F5]">
            <div class="flex items-center justify-between">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                    <div class="w-9 h-9 flex items-center justify-center shrink-0">
                        <img src="{{ asset('assets/CLSU-LOGO-removebg.png') }}" alt="CLSU Logo"
                            class="w-9 h-9 object-contain">
                    </div>
                    <div>
                        <h1 class="brand-title text-2xl leading-tight text-[#09090b] font-bold">C.S.M.S.</h1>
                    </div>
                </a>
                <button id="sidebar-close"
                    class="lg:hidden text-[#A5B2BD] hover:text-[#394056] transition p-1.5 rounded-[8px] hover:bg-[#F1F3F5]">
                    <i class="bx bx-x text-xl"></i>
                </button>
            </div>
        </div>

        {{-- Nav --}}
        <div class="flex-1 overflow-y-auto no-scrollbar py-3 px-2.5 space-y-5">
            @auth
                <nav>
                    <p class="nav-label">Home</p>
                    <a href="{{ route('dashboard') }}"
                        class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bx bx-grid-alt nav-icon"></i>
                        Dashboard
                    </a>
                </nav>

                @if ($user->hasRole('admin') || $user->hasRole('faculty') || $user->hasRole('ovpaa'))
                    <nav>
                        <p class="nav-label">Syllabus</p>
                        <a href="{{ route('syllabus.index') }}"
                            class="nav-link {{ request()->routeIs('syllabus.*') ? 'active' : '' }}">
                            <i class="bx bxs-notepad nav-icon"></i>
                            Syllabi
                        </a>
                    </nav>
                @endif

                @if ($user->hasRole('admin') || $user->hasRole('chair') || $user->hasRole('dean') || $user->hasRole('ovpaa'))
                    <nav>
                        <p class="nav-label">Academic Setup</p>

                        @if ($user->hasRole('admin') || $user->hasRole('ovpaa'))
                            <a href="{{ route('academic.calendars.index') }}"
                                class="nav-link {{ request()->routeIs('academic.calendars.*', 'academic.calendar.*') ? 'active' : '' }}">
                                <i class="bx bxs-calendar nav-icon"></i>
                                Academic Calendars
                            </a>
                        @endif

                        @if ($user->hasRole('admin') || $user->hasRole('dean'))
                            <a href="{{ route('goal.index') }}"
                                class="nav-link {{ request()->routeIs('goal.*') ? 'active' : '' }}">
                                <i class="bx bxs-bullseye nav-icon"></i>
                                College Goals
                            </a>
                        @endif

                        @if ($user->hasRole('admin') || $user->hasRole('chair'))
                            <a href="{{ route('objective.index') }}"
                                class="nav-link {{ request()->routeIs('objective.*') ? 'active' : '' }}">
                                <i class="bx bx-list-check nav-icon"></i>
                                Department Objectives
                            </a>
                            <a href="{{ route('programs.index') }}"
                                class="nav-link {{ request()->routeIs('programs.*') ? 'active' : '' }}">
                                <i class="bx bxs-network-chart nav-icon"></i>
                                PEOs &amp; POs
                            </a>
                            <a href="{{ route('courses.index') }}"
                                class="nav-link {{ request()->routeIs('courses.*') ? 'active' : '' }}">
                                <i class="bx bxs-book-content nav-icon"></i>
                                Courses
                            </a>
                        @endif
                    </nav>
                @endif

                @if ($user->hasRole('admin'))
                    <nav>
                        <p class="nav-label">Administration</p>
                        <a href="{{ route('organizational.colleges.index') }}"
                            class="nav-link {{ request()->routeIs('organizational.*') ? 'active' : '' }}">
                            <i class="bx bx-sitemap nav-icon"></i>
                            University Faculties
                        </a>
                        <a href="{{ route('academic.structure.index') }}"
                            class="nav-link {{ request()->routeIs('academic.structure.*') ? 'active' : '' }}">
                            <i class="bx bxs-layer nav-icon"></i>
                            Academic Structure
                        </a>
                        <a href="{{ route('accounts.approval') }}"
                            class="nav-link {{ request()->routeIs('accounts.approval') ? 'active' : '' }}">
                            <i class="bx bxs-user-detail nav-icon"></i>
                            User Management
                        </a>
                        <a href="{{ route('audit.logs.index') }}"
                            class="nav-link {{ request()->routeIs('audit.logs.*') ? 'active' : '' }}">
                            <i class="bx bx-history nav-icon"></i>
                            Audit Logs
                        </a>
                    </nav>
                @endif
            @endauth

            @guest
                <nav>
                    <a href="{{ route('auth.show') }}" class="nav-link">
                        <i class="bx bxs-log-in-circle nav-icon"></i>
                        Login / Register
                    </a>
                </nav>
            @endguest
        </div>

        {{-- User card + logout --}}
        @auth
            <div class="shrink-0 border-t border-[#F1F3F5] p-3.5">
                <div class="flex items-center gap-2.5 p-2 rounded-[10px] bg-[#FAFDFB] border border-[#F1F3F5]">
                    <div class="w-8 h-8 rounded-full bg-[#EDFFF8] border border-[#AEFFE2]
                                flex items-center justify-center shrink-0">
                        <i class="bx bxs-user text-[#06754E] text-sm"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[13px] font-semibold text-[#09090b] truncate">{{ $user->name ?? 'User' }}</p>
                        <p class="text-[11px] text-[#71717a] capitalize">{{ $user->roles->first()?->name ?? 'Member' }}</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" title="Sign out"
                            class="flex items-center justify-center w-8 h-8 rounded-[8px]
                                   text-[#A5B2BD] hover:text-[#e11d48] hover:bg-[#fff1f2]
                                   transition-colors duration-150">
                            <i class="bx bxs-log-out text-base"></i>
                        </button>
                    </form>
                </div>
            </div>
        @endauth
    </aside>

    {{-- ── Main content column ──────────────────────────────────────── --}}
    <div class="flex flex-col min-h-screen {{ $isWizardRoute ? '' : 'lg:pl-60' }}">

        {{-- Navbar --}}
        <header class="sticky top-0 z-20 bg-white border-b border-[#E3E8EB]
                       {{ $isWizardRoute ? 'hidden' : '' }}"
                style="box-shadow: 0 1px 2px rgba(16,24,40,0.04), 0 1px 3px rgba(16,24,40,0.06);">
            <div class="px-4 sm:px-6 h-14 flex items-center justify-between gap-4">

                <div class="flex items-center gap-3 min-w-0">
                    <button id="sidebar-open"
                        class="lg:hidden {{ $isWizardRoute ? 'hidden' : 'inline-flex' }}
                               items-center justify-center h-8 w-8 rounded-[7px]
                               text-[#72809E] hover:text-[#394056] hover:bg-[#F1F3F5]
                               transition-all duration-150 shrink-0">
                        <i class="bx bx-menu text-xl"></i>
                    </button>
                    <span class="hidden sm:block h-5 w-px bg-[#E3E8EB] shrink-0"></span>
                    <div class="hidden sm:block min-w-0">
                        <p class="text-[9px] uppercase tracking-[0.28em] text-[#93A1AF] leading-none">
                            Central Luzon State University
                        </p>
                        <h1 class="brand-title text-[13.5px] font-bold text-[#394056] leading-tight truncate">
                            Course Syllabus Management
                        </h1>
                    </div>
                </div>

                @auth
                    <a href="{{ route('profile.index') }}"
                        class="flex items-center gap-2 shrink-0 rounded-full
                               bg-white border border-[#E3E8EB] pl-1.5 pr-3 py-1
                               hover:bg-[#EDFFF8] hover:border-[#00C075]
                               transition-all duration-200 group
                               {{ $isWizardRoute ? 'hidden' : '' }}">
                        <div class="w-7 h-7 rounded-full bg-[#D5FFF0] border border-[#00C075]
                                    flex items-center justify-center shrink-0">
                            <i class="bx bxs-user text-[#06754E] text-sm"></i>
                        </div>
                        <div class="hidden sm:block text-left leading-tight">
                            <p class="text-[12.5px] font-semibold text-[#394056] group-hover:text-[#06754E] truncate max-w-36">
                                {{ $user->name ?? 'User' }}
                            </p>
                        </div>
                        <i class="bx bx-chevron-down text-[#C1C8D4] text-sm hidden sm:block group-hover:text-[#00965F] transition-colors duration-200"></i>
                    </a>
                @endauth
            </div>
        </header>

        <main class="flex-1 w-full">
            @yield('content')
        </main>

        {{-- @if (!$isWizardRoute) --}}
            @include('layouts.footer')
        {{-- @endif --}}
    </div>

    <style>

        body::before {
            content: '';
            position: fixed;
            inset: -12%;
            z-index: -1;
            pointer-events: none;
            background:
                radial-gradient(620px 620px at 18% 15%, rgba(0, 216, 139, 0.25) 0%, transparent 60%),
                radial-gradient(520px 520px at 88% 8%,  rgba(49, 151, 214, 0.15) 0%, transparent 60%),
                radial-gradient(680px 680px at 78% 78%, rgba(112, 255, 204, 0.25) 0%, transparent 60%),
                radial-gradient(460px 460px at 8% 88%,  rgba(174, 223, 255, 0.15) 0%, transparent 60%),
                radial-gradient(520px 520px at 48% 45%, rgba(43, 253, 176, 0.13) 0%, transparent 60%);
            filter: blur(70px) saturate(115%);
            animation: drift 26s ease-in-out infinite alternate;
        }

        @keyframes drift {
            0%   { transform: translate3d(0, 0, 0) scale(1); }
            50%  { transform: translate3d(-2.5%, 2%, 0) scale(1.06); }
            100% { transform: translate3d(2.5%, -2%, 0) scale(1); }
        }

        @media (prefers-reduced-motion: reduce) {
            body::before { animation: none; }
        }

        /* Fine grid texture on top of the blob glow — adds tactile detail without competing with it */
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            z-index: -1;
            pointer-events: none;
            background-image:
                repeating-linear-gradient(0deg,  transparent, transparent 5px, rgba(6, 117, 78, 0.03) 5px, rgba(6, 117, 78, 0.03) 6px, transparent 6px, transparent 15px),
                repeating-linear-gradient(90deg, transparent, transparent 5px, rgba(6, 117, 78, 0.03) 5px, rgba(6, 117, 78, 0.03) 6px, transparent 6px, transparent 15px),
                repeating-linear-gradient(0deg,  transparent, transparent 10px, rgba(57, 64, 86, 0.016) 10px, rgba(57, 64, 86, 0.016) 11px, transparent 11px, transparent 30px),
                repeating-linear-gradient(90deg, transparent, transparent 10px, rgba(57, 64, 86, 0.016) 10px, rgba(57, 64, 86, 0.016) 11px, transparent 11px, transparent 30px);
        }

        /* Oswald for the brand mark and nav section labels — everything else stays the body font */
        .brand-title {
            font-family: 'Oswald', sans-serif;
            letter-spacing: -0.01em;
        }

        /* ─── Sidebar nav links ──────────────────────────────────────────────────── */
        .nav-label {
            font-family: 'Oswald', sans-serif;
            font-size: 10.5px;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #A5B2BD;
            padding: 0 1rem 0.35rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.5rem 1rem;
            color: #394056;
            text-decoration: none;
            font-size: 0.8125rem;
            font-weight: 500;
            border-left: 3px solid transparent;
            border-radius: 0;
            transition: background-color 0.18s ease, border-color 0.18s ease, color 0.18s ease;
            background: transparent;
        }

        .nav-icon {
            font-size: 1.1rem;
            opacity: 0.65;
            transition: opacity 0.18s ease;
            flex-shrink: 0;
        }

        .nav-link:hover {
            color: #06754E;
            border-left-color: rgba(0, 216, 139, 0.35);
            background: #FAFDFB;
        }

        .nav-link:hover .nav-icon {
            opacity: 1;
        }

        .nav-link:focus-visible {
            outline: 2px solid #00D88B;
            outline-offset: -2px;
        }

        .nav-link.active {
            color: #06754E;
            font-weight: 700;
            border-left: 3px solid #00D88B;
            border-radius: 0;
            background: linear-gradient(90deg, #EDFFF8 0%, rgba(237,255,248,0) 85%);
        }

        .nav-link.active .nav-icon {
            opacity: 1;
        }
    </style>

    @livewireScripts
    <script src="https://cdn.jsdelivr.net/gh/livewire/sortable@v1.x.x/dist/livewire-sortable.js"></script>
    @stack('scripts')

    {{-- #22 Session expiry modal — shown when Livewire gets a 419 (CSRF/session expired).
         Warns the user before they lose unsaved Quill editor content. --}}
    <div id="session-expired-modal"
         class="hidden fixed inset-0 z-[9999] flex items-center justify-center">
        <div class="absolute inset-0 bg-[#09090b]/50 backdrop-blur-[3px]"></div>
        <div class="relative bg-white rounded-[16px] border border-[#e4e4e7] p-6 w-80 text-center"
             style="box-shadow: 0 8px 40px rgba(0,0,0,0.14);">
            <div class="flex items-center justify-center w-12 h-12 rounded-full bg-amber-50 border border-amber-200 mx-auto mb-4">
                <i class="bx bx-time-five text-2xl text-amber-500"></i>
            </div>
            <h3 class="text-[14px] font-bold text-[#09090b] mb-1">Session Expired</h3>
            <p class="text-[12px] text-[#71717a] mb-4">
                Your session has expired. Any unsaved content in open editors may be lost.
                Please save your work, then refresh the page to continue.
            </p>
            <button onclick="window.location.reload()"
                class="w-full px-4 py-2 rounded-[10px] bg-[#09090b] text-white text-[13px] font-semibold hover:bg-[#18181b] transition">
                Refresh &amp; Log In
            </button>
        </div>
    </div>

    <script>
        // Listen for Livewire request errors — a 419 means the CSRF token
        // expired (session timed out). Show the modal instead of silently failing.
        document.addEventListener('livewire:request-error', (e) => {
            if (e.detail?.status === 419) {
                document.getElementById('session-expired-modal')?.classList.remove('hidden');
            }
        });
    </script>

    <script>
        const sidebar  = document.getElementById('app-sidebar');
        const overlay  = document.getElementById('sidebar-overlay');
        const openBtn  = document.getElementById('sidebar-open');
        const closeBtn = document.getElementById('sidebar-close');

        const openSidebar  = () => { sidebar?.classList.remove('-translate-x-full'); overlay?.classList.remove('hidden'); };
        const closeSidebar = () => { sidebar?.classList.add('-translate-x-full');    overlay?.classList.add('hidden'); };

        openBtn?.addEventListener('click', openSidebar);
        closeBtn?.addEventListener('click', closeSidebar);
        overlay?.addEventListener('click', closeSidebar);

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                overlay?.classList.add('hidden');
                sidebar?.classList.remove('-translate-x-full');
            } else {
                sidebar?.classList.add('-translate-x-full');
            }
        });
    </script>
</body>
</html>
