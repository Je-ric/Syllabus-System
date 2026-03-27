<!DOCTYPE html>
<html lang="en">

<head>
    @include('includes.head-assets')
</head>

<body class="min-h-screen">

    @if (session('toast'))
        <x-feedback-status.toast :message="session('toast')['message']" :type="session('toast')['type']" />
    @endif
    <x-feedback-status.toast />

    @php
        $isWizardRoute = request()->routeIs('syllabus.wizard');
        $user = Auth::user();
    @endphp

    <div class="min-h-screen">

        {{-- Mobile overlay --}}
        <div id="sidebar-overlay"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-30 hidden lg:hidden {{ $isWizardRoute ? 'hidden' : '' }}">
        </div>

        {{-- ── Sidebar ──────────────────────────────────────────────────────── --}}
        <aside id="app-sidebar"
            class="fixed inset-y-0 left-0 z-40 w-64 flex flex-col
                      transform -translate-x-full lg:translate-x-0
                      transition-transform duration-300 ease-out
                      {{ $isWizardRoute ? 'hidden lg:hidden' : '' }}"
            style="background: linear-gradient(180deg, #002a0c 0%, #003a10 40%, #004d16 100%);">

            {{-- Brand header --}}
            <div class="shrink-0 px-5 py-5 border-b border-white/10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-9 h-9 rounded-lg bg-[#ffd700]/15 border border-[#ffd700]/30
                                    flex items-center justify-center shrink-0">
                            <i class="bx bxs-graduation text-[#ffd700] text-lg"></i>
                        </div>
                        <div>
                            <p
                                class="text-[10px] uppercase tracking-[0.25em] font-semibold text-[#ffd700]/80 leading-tight">
                                CLSU
                            </p>
                            <h1 class="brand-title text-[1.15rem] leading-tight text-white">
                                Syllabus System
                            </h1>
                        </div>
                    </div>
                    <button id="sidebar-close"
                        class="lg:hidden text-white/50 hover:text-white transition p-1 rounded-md hover:bg-white/10">
                        <i class="bx bx-x text-xl"></i>
                    </button>
                </div>
            </div>

            {{-- Nav --}}
            <div class="flex-1 overflow-y-auto no-scrollbar py-4 space-y-5">

                @auth
                    {{-- Main navigation --}}
                    <nav class="space-y-0.5 px-0">
                        <p class="text-[10px] uppercase tracking-[0.3em] text-white/35 font-semibold px-4 mb-2">
                            Navigation
                        </p>

                        @if ($user->hasRole('admin') || $user->hasRole('faculty'))
                            <a href="{{ route('syllabus.index') }}"
                                class="{{ request()->routeIs('syllabus.*') ? 'active' : '' }}">
                                <i class="bx bxs-notepad"></i>
                                Syllabi
                            </a>
                        @endif

                        @if ($user->hasRole('admin'))
                            <a href="{{ route('accounts.approval') }}"
                                class="{{ request()->routeIs('accounts.approval') ? 'active' : '' }}">
                                <i class="bx bxs-user-detail"></i>
                                User Management
                            </a>
                            <a href="{{ route('organizational.colleges.index') }}"
                                class="{{ request()->routeIs('organizational.*') ? 'active' : '' }}">
                                <i class="bx bx-sitemap"></i>
                                University Faculties
                            </a>
                            <a href="{{ route('academic.structure.index') }}"
                                class="{{ request()->routeIs('academic.structure.*') ? 'active' : '' }}">
                                <i class="bx bxs-layer"></i>
                                Academic Structure
                            </a>
                            <a href="{{ route('academic.calendars.index') }}"
                                class="{{ request()->routeIs('academic.calendars.*', 'academic.calendar.*') ? 'active' : '' }}">
                                <i class="bx bxs-calendar"></i>
                                Academic Calendars
                            </a>
                            <a href="{{ route('audit.logs.index') }}"
                                class="{{ request()->routeIs('audit.logs.*') ? 'active' : '' }}">
                                <i class="bx bx-history"></i>
                                Audit Logs
                            </a>
                        @endif

                        @if ($user->hasRole('admin') || $user->hasRole('dean'))
                            <a href="{{ route('goal.index') }}"
                                class="{{ request()->routeIs('goal.*') ? 'active' : '' }}">
                                <i class="bx bxs-bullseye"></i>
                                College Goals
                            </a>
                        @endif

                        @if ($user->hasRole('admin') || $user->hasRole('chair'))
                            <a href="{{ route('objective.index') }}"
                                class="{{ request()->routeIs('objective.*') ? 'active' : '' }}">
                                <i class="bx bx-list-check"></i>
                                Department Objectives
                            </a>
                            <a href="{{ route('programs.index') }}"
                                class="{{ request()->routeIs('programs.*') ? 'active' : '' }}">
                                <i class="bx bxs-network-chart"></i>
                                PEOs &amp; POs
                            </a>
                            <a href="{{ route('courses.index') }}"
                                class="{{ request()->routeIs('courses.*') ? 'active' : '' }}">
                                <i class="bx bxs-book-content"></i>
                                Courses
                            </a>
                        @endif
                    </nav>
                @endauth

                @guest
                    <nav class="px-0">
                        <a href="{{ route('auth.show') }}"
                            class="flex items-center gap-2.5 px-4 py-2.5 text-white/70 hover:text-white hover:bg-white/10 rounded-lg mx-3 transition text-sm">
                            <i class="bx bxs-log-in-circle text-lg"></i>
                            Login / Register
                        </a>
                    </nav>
                @endguest
            </div>

            {{-- User card + logout --}}
            @auth
                <div class="shrink-0 border-t border-white/10 p-4">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg
                                        bg-red-600/70 border-red-600/50 hover:bg-red-600/90 hover:border-red-600/80
                                       text-xs font-semibold text-white/90 border hover:text-white
                                       transition-all duration-150">
                            <i class="bx bxs-log-out text-sm"></i>
                            Sign Out
                        </button>
                    </form>
                </div>
            @endauth
        </aside>

        {{-- ── Main content ─────────────────────────────────────────────────── --}}
        <div class="flex flex-col min-h-screen {{ $isWizardRoute ? '' : 'lg:ml-64' }}">

            {{-- Navbar --}}
            <header class="sticky top-0 z-20 border-b border-white/10"
                style="background: linear-gradient(90deg, #003a10 0%, #006622 60%, #009639 100%);">
                <div class="px-4 sm:px-6 h-14 flex items-center justify-between gap-4">

                    {{-- Left: hamburger + page context --}}
                    <div class="flex items-center gap-3 min-w-0">
                        <button id="sidebar-open"
                            class="lg:hidden {{ $isWizardRoute ? 'hidden' : 'inline-flex' }}
                                       items-center justify-center h-8 w-8 rounded-lg
                                       text-white/70 hover:text-white hover:bg-white/10 transition shrink-0">
                            <i class="bx bx-menu text-xl"></i>
                        </button>

                        {{-- Vertical divider --}}
                        <span class="hidden sm:block h-5 w-px bg-white/20 shrink-0"></span>

                        <div class="hidden sm:block min-w-0">
                            <p class="text-[10px] uppercase tracking-[0.25em] text-white/50 leading-none">
                                Central Luzon State University
                            </p>
                            <h1 class="brand-title text-base text-white leading-tight truncate">
                                Course Syllabus Management
                            </h1>
                        </div>
                    </div>

                    {{-- Right: profile pill --}}
                    @auth
                        <a href="{{ route('profile.index') }}"
                            class="flex items-center gap-2.5 shrink-0
                                  rounded-xl bg-white/10 border border-white/15
                                  px-3 py-1.5 hover:bg-white/20 hover:border-white/25
                                  transition-all duration-150 group {{ $isWizardRoute ? 'hidden lg:hidden' : '' }}">

                            {{-- Avatar --}}
                            <div
                                class="w-7 h-7 rounded-full bg-[#ffd700]/25 border border-[#ffd700]/40
                                        flex items-center justify-center shrink-0">
                                <i class="bx bxs-user text-[#ffd700] text-sm"></i>
                            </div>

                            {{-- Name + role --}}
                            <div class="hidden sm:block text-left leading-tight">
                                <p class="text-xs font-semibold text-white group-hover:text-white/90 truncate max-w-35">
                                    {{ $user->name ?? 'User' }}
                                </p>
                            </div>

                            <i
                                class="bx bx-chevron-down text-white/40 text-sm hidden sm:block group-hover:text-white/60 transition"></i>
                        </a>
                    @endauth
                </div>
            </header>

            <main class="flex-1 w-full overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
    <script>
        const sidebar = document.getElementById('app-sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const openBtn = document.getElementById('sidebar-open');
        const closeBtn = document.getElementById('sidebar-close');

        const openSidebar = () => {
            if (!sidebar || !overlay) return;
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        };

        const closeSidebar = () => {
            if (!sidebar || !overlay) return;
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        };

        if (openBtn) openBtn.addEventListener('click', openSidebar);
        if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
        if (overlay) overlay.addEventListener('click', closeSidebar);

        window.addEventListener('resize', () => {
            if (!sidebar || !overlay) return;
            if (window.innerWidth >= 1024) {
                overlay.classList.add('hidden');
                sidebar.classList.remove('-translate-x-full');
            } else {
                sidebar.classList.add('-translate-x-full');
            }
        });
    </script>
</body>

</html>
