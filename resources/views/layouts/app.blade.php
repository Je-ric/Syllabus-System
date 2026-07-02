<!DOCTYPE html>
<html lang="en">
<head>
    @include('includes.head-assets')
</head>

<body class="min-h-screen bg-slate-100">

    @php
        $isWizardRoute = request()->routeIs('syllabus.wizard');
        $user = Auth::user();
    @endphp

    {{-- @if (!$isWizardRoute)
        <x-screen-loader />
    @endif --}}

    @if (session('toast'))
        <x-feedback-status.toast :message="session('toast')['message']" :type="session('toast')['type']" />
    @endif
    <x-feedback-status.toast />


    {{-- Mobile overlay --}}
    <div id="sidebar-overlay"
        class="fixed inset-0 bg-black/40 backdrop-blur-sm z-30 hidden lg:hidden
               {{ $isWizardRoute ? 'hidden!' : '' }}">
    </div>

    {{-- ── Sidebar ─────────────────────────────────────────────────── --}}
    <aside id="app-sidebar"
        class="fixed inset-y-0 left-0 z-40 w-60 bg-white flex flex-col
               border-r border-slate-200
               transform -translate-x-full lg:translate-x-0
               transition-transform duration-300 ease-out
               {{ $isWizardRoute ? 'hidden lg:hidden' : '' }}">

        {{-- Brand header --}}
        <div class="shrink-0 px-4 pt-5 pb-4 border-b border-slate-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 overflow-hidden">
                        <img
                            src="{{ asset('assets/CLSU-LOGO-removebg.png') }}"
                            alt="CLSU Logo"
                            class="w-11 h-11 object-contain"
                        >
                    </div>
                    <div>
                        <h1 class="brand-title text-2xl leading-tight text-slate-900 font-bold">
                            C.S.M.S.
                        </h1>
                    </div>
                </div>
                <button id="sidebar-close"
                    class="lg:hidden text-slate-400 hover:text-slate-700 transition p-1.5 rounded-lg hover:bg-slate-100">
                    <i class="bx bx-x text-xl"></i>
                </button>
            </div>
        </div>

        {{-- Nav --}}
        <div class="flex-1 overflow-y-auto no-scrollbar py-3 px-2.5 space-y-5">
            @auth
                {{-- ── Group 1: Syllabus Work ─────── --}}
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

                {{-- ── Group 2: Academic Setup ──────── --}}
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

                {{-- ── Group 3: Administration ──────── --}}
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
            <div class="shrink-0 border-t border-slate-100 p-3.5">
                <div class="flex items-center gap-2.5 mb-2.5 px-1">
                    <div class="w-8 h-8 rounded-full bg-emerald-50 border border-emerald-200
                                flex items-center justify-center shrink-0">
                        <i class="bx bxs-user text-emerald-600 text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[12.5px] font-semibold text-slate-800 truncate">{{ $user->name ?? 'User' }}</p>
                        <p class="text-[10.5px] text-slate-400 capitalize">{{ $user->roles->first()?->name ?? 'Member' }}</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg 
                               bg-red-50 border border-red-100 hover:bg-red-100 hover:border-red-200
                               text-[11.5px] font-semibold text-red-600
                               transition-all duration-150">
                        <i class="bx bxs-log-out text-sm"></i>
                        Sign Out
                    </button>
                </form>
            </div>
        @endauth
    </aside>

    {{-- ── Main content column ─────────────────────────────────── --}}
    <div class="flex flex-col min-h-screen {{ $isWizardRoute ? '' : 'lg:pl-60' }}">

        {{-- Navbar --}}
        <header class="sticky top-0 z-20 bg-white border-b border-slate-200
                       {{ $isWizardRoute ? 'hidden' : '' }}">
            <div class="px-4 sm:px-6 h-14 flex items-center justify-between gap-4">

                {{-- Left --}}
                <div class="flex items-center gap-3 min-w-0">
                    <button id="sidebar-open"
                        class="lg:hidden {{ $isWizardRoute ? 'hidden' : 'inline-flex' }}
                               items-center justify-center h-8 w-8 rounded-lg
                               text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition shrink-0">
                        <i class="bx bx-menu text-xl"></i>
                    </button>
                    <span class="hidden sm:block h-5 w-px bg-slate-200 shrink-0"></span>
                    <div class="hidden sm:block min-w-0">
                        <p class="text-[9.5px] uppercase tracking-[0.28em] text-slate-400 leading-none">
                            Central Luzon State University
                        </p>
                        <h1 class="brand-title text-[14px] font-bold text-slate-900 leading-tight truncate">
                            Course Syllabus Management
                        </h1>
                    </div>
                </div>

                {{-- Right: profile pill --}}
                @auth
                    <a href="{{ route('profile.index') }}"
                        class="flex items-center gap-2 shrink-0 rounded-full
                               bg-slate-50 border border-slate-200 pl-1.5 pr-3 py-1
                               hover:bg-slate-100 hover:border-slate-300
                               transition-all duration-150 group
                               {{ $isWizardRoute ? 'hidden' : '' }}">
                        <div class="w-7 h-7 rounded-full bg-emerald-50 border border-emerald-200
                                    flex items-center justify-center shrink-0">
                            <i class="bx bxs-user text-emerald-600 text-sm"></i>
                        </div>
                        <div class="hidden sm:block text-left leading-tight">
                            <p class="text-[12.5px] font-semibold text-slate-800 group-hover:text-slate-900 truncate max-w-36">
                                {{ $user->name ?? 'User' }}
                            </p>
                        </div>
                        <i class="bx bx-chevron-down text-slate-400 text-sm hidden sm:block group-hover:text-slate-600 transition"></i>
                    </a>
                @endauth
            </div>
        </header>

        <main class="flex-1 w-full bg-slate-100">
            @yield('content')
        </main>

        {{-- Footer --}}
        @if (!$isWizardRoute)
            <footer class="border-t border-slate-200 bg-white shrink-0">
                <div class="px-4 sm:px-6 py-3 flex flex-col sm:flex-row items-center justify-between gap-2">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0"
                             style="background: linear-gradient(135deg, #002a0c 0%, #009639 100%);">
                            <i class="bx bxs-graduation text-[#ffd700] text-sm"></i>
                        </div>
                        <div class="leading-tight">
                            <span class="text-[12px] font-bold text-slate-800">CLSU</span>
                            <span class="text-[11px] text-slate-400 ml-1">Course Syllabus Management System</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-[11px] text-slate-400">
                        <span><i class="bx bx-copyright mr-0.5"></i>{{ date('Y') }} Central Luzon State University</span>
                    </div>
                </div>
            </footer>
        @endif
    </div>

    <style>
        
    </style>

    @livewireScripts
    <script src="https://cdn.jsdelivr.net/gh/livewire/sortable@v1.x.x/dist/livewire-sortable.js"></script>
    @stack('scripts')
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