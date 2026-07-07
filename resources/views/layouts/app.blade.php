<!DOCTYPE html>
<html lang="en">
<head>
    @include('includes.head-assets')
</head>

<body class="min-h-screen bg-[#f4f4f5]">

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
        class="fixed inset-y-0 left-0 z-40 w-60 bg-white flex flex-col
               border-r border-[#e4e4e7]
               transform -translate-x-full lg:translate-x-0
               transition-transform duration-300 ease-out
               {{ $isWizardRoute ? 'hidden lg:hidden' : '' }}">

        {{-- Brand header --}}
        <div class="shrink-0 px-4 pt-5 pb-4 border-b border-[#f4f4f5]">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-[10px] flex items-center justify-center shrink-0 overflow-hidden">
                        <img src="{{ asset('assets/CLSU-LOGO-removebg.png') }}" alt="CLSU Logo"
                            class="w-11 h-11 object-contain">
                    </div>
                    <div>
                        <h1 class="brand-title text-2xl leading-tight text-[#09090b] font-bold">C.S.M.S.</h1>
                    </div>
                </div>
                <button id="sidebar-close"
                    class="lg:hidden text-[#a1a1aa] hover:text-[#09090b] transition p-1.5 rounded-[8px] hover:bg-[#f4f4f5]">
                    <i class="bx bx-x text-xl"></i>
                </button>
            </div>
        </div>

        {{-- Nav --}}
        <div class="flex-1 overflow-y-auto no-scrollbar py-3 px-2.5 space-y-5">
            @auth
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
            <div class="shrink-0 border-t border-[#f4f4f5] p-3.5">
                <div class="flex items-center gap-2.5 mb-2.5 px-1">
                    <div class="w-8 h-8 rounded-full bg-[#f0fdf4] border border-[#d1fae5]
                                flex items-center justify-center shrink-0">
                        <i class="bx bxs-user text-[#16a34a] text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[12.5px] font-semibold text-[#09090b] truncate">{{ $user->name ?? 'User' }}</p>
                        <p class="text-[10.5px] text-[#a1a1aa] capitalize">{{ $user->roles->first()?->name ?? 'Member' }}</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-[8px]
                               bg-[#fff1f2] border border-[#fecdd3] hover:bg-[#ffe4e6] hover:border-[#fda4af]
                               text-[11.5px] font-semibold text-[#e11d48]
                               transition-all duration-150">
                        <i class="bx bxs-log-out text-sm"></i>
                        Sign Out
                    </button>
                </form>
            </div>
        @endauth
    </aside>

    {{-- ── Main content column ──────────────────────────────────────── --}}
    <div class="flex flex-col min-h-screen {{ $isWizardRoute ? '' : 'lg:pl-60' }}">

        {{-- Navbar --}}
        <header class="sticky top-0 z-20 bg-white border-b border-[#e4e4e7]
                       {{ $isWizardRoute ? 'hidden' : '' }}"
                style="box-shadow: 0 1px 4px rgba(0,0,0,0.04);">
            <div class="px-4 sm:px-6 h-14 flex items-center justify-between gap-4">

                <div class="flex items-center gap-3 min-w-0">
                    <button id="sidebar-open"
                        class="lg:hidden {{ $isWizardRoute ? 'hidden' : 'inline-flex' }}
                               items-center justify-center h-8 w-8 rounded-[8px]
                               text-[#71717a] hover:text-[#09090b] hover:bg-[#f4f4f5] transition shrink-0">
                        <i class="bx bx-menu text-xl"></i>
                    </button>
                    <span class="hidden sm:block h-5 w-px bg-[#e4e4e7] shrink-0"></span>
                    <div class="hidden sm:block min-w-0">
                        <p class="text-[9.5px] uppercase tracking-[0.28em] text-[#a1a1aa] leading-none">
                            Central Luzon State University
                        </p>
                        <h1 class="brand-title text-[14px] font-bold text-[#09090b] leading-tight truncate">
                            Course Syllabus Management
                        </h1>
                    </div>
                </div>

                @auth
                    <a href="{{ route('profile.index') }}"
                        class="flex items-center gap-2 shrink-0 rounded-full
                               bg-[#fafafa] border border-[#e4e4e7] pl-1.5 pr-3 py-1
                               hover:bg-[#f4f4f5] hover:border-[#d4d4d8]
                               transition-all duration-150 group
                               {{ $isWizardRoute ? 'hidden' : '' }}">
                        <div class="w-7 h-7 rounded-full bg-[#f0fdf4] border border-[#d1fae5]
                                    flex items-center justify-center shrink-0">
                            <i class="bx bxs-user text-[#16a34a] text-sm"></i>
                        </div>
                        <div class="hidden sm:block text-left leading-tight">
                            <p class="text-[12.5px] font-semibold text-[#09090b] group-hover:text-[#18181b] truncate max-w-36">
                                {{ $user->name ?? 'User' }}
                            </p>
                        </div>
                        <i class="bx bx-chevron-down text-[#a1a1aa] text-sm hidden sm:block group-hover:text-[#52525b] transition"></i>
                    </a>
                @endauth
            </div>
        </header>

        <main class="flex-1 w-full bg-[#f4f4f5]">
            @yield('content')
        </main>

        @if (!$isWizardRoute)
            <footer class="border-t border-[#e4e4e7] bg-white shrink-0">
                <div class="px-4 sm:px-6 py-3 flex flex-col sm:flex-row items-center justify-between gap-2">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-[8px] flex items-center justify-center shrink-0 bg-[#09090b]">
                            <i class="bx bxs-graduation text-[#ffd700] text-sm"></i>
                        </div>
                        <div class="leading-tight">
                            <span class="text-[12px] font-bold text-[#09090b]">CLSU</span>
                            <span class="text-[11px] text-[#a1a1aa] ml-1">Course Syllabus Management System</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-[11px] text-[#a1a1aa]">
                        <span><i class="bx bx-copyright mr-0.5"></i>{{ date('Y') }} Central Luzon State University</span>
                    </div>
                </div>
            </footer>
        @endif
    </div>

    <style></style>

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
