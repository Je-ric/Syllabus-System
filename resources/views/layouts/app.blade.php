<!DOCTYPE html>
<html lang="en">

<head>
    @include('includes.head-assets')
</head>

<body class="min-h-screen">

    {{-- <x-screen-loader /> --}}

    {{-- Hide sidebar when the route is syllabus.wizard--}}
    @php
        $isWizardRoute = request()->routeIs('syllabus.wizard');
    @endphp

    {{-- Controller --}}
    @if (session('toast'))
        <x-feedback-status.toast
            :message="session('toast')['message']"
            :type="session('toast')['type']" />
    @endif
    {{-- Livewire --}}
    <x-feedback-status.toast />

    <div class="min-h-screen">
        <div id="sidebar-overlay" class="fixed inset-0 bg-green-900/50 backdrop-blur-sm z-30 hidden lg:hidden {{ $isWizardRoute ? 'hidden' : '' }}"></div>

        <aside id="app-sidebar"
                class="fixed inset-y-0 left-0 z-40 w-72
                    bg-white shadow-2xl border-r border-slate-200
                    transform -translate-x-full lg:translate-x-0
                    transition-transform duration-300 ease-out
                    h-full overflow-y-auto no-scrollbar
                    {{ $isWizardRoute ? 'hidden lg:hidden' : '' }}">
            <div class="sticky top-0 z-20 px-6 py-6 border-b border-slate-200 bg-white text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.3em] font-semibold text-amber-400">Central Luzon <br> State University</p>
                        <h1 class="brand-title text-2xl mt-1 text-green-700">Syllabus System</h1>
                    </div>
                    <button id="sidebar-close" class="lg:hidden text-white/80 hover:text-white transition">
                        <i class="bx bx-x text-2xl"></i>
                    </button>
                </div>
            </div>

            <div class="pl-5 py-6 space-y-6">

                <nav class="space-y-2 text-sm">
                    @auth
                        <p class="text-xs uppercase tracking-[0.3em] text-slate-400 px-3">Navigation</p>

                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('faculty'))
                            <a href="{{ route('syllabus.index') }}"
                                class="{{ request()->routeIs('syllabus.index') ? 'active' : '' }}">
                                <i class="bx bxs-notepad text-lg"></i>
                                Syllabi
                            </a>
                        @endif

                        @if(auth()->user()->hasRole('admin'))
                            <a href="{{ route('accounts.approval') }}"
                                class="{{ request()->routeIs('accounts.approval') ? 'active' : '' }}">
                                <i class="bx bxs-user-detail text-lg"></i>
                                User Management
                            </a>
                            <a href="{{ route('organizational.colleges.index') }} "
                                class="{{ request()->routeIs('organizational.colleges.index') ? 'active' : '' }}">
                                <i class="bx bx-sitemap text-lg"></i>
                                University Faculties
                            </a>
                            <a href="{{ route('academic.structure.index') }}"
                                class="{{ request()->routeIs('academic.structure.index') ? 'active' : '' }}">
                                <i class="bx bxs-layer text-lg"></i>
                                Academic Structure
                            </a>
                            <a href="{{ route('academic.calendars.index') }}"
                                class="{{ request()->routeIs('academic.calendars.index') ? 'active' : '' }}">
                                <i class="bx bxs-calendar text-lg"></i>
                                Academic Calendars
                            </a>
                            <a href="{{ route('audit.logs.index') }}"
                                class="{{ request()->routeIs('audit.logs.index') ? 'active' : '' }}">
                                <i class="bx bx-history text-lg"></i>
                                Audit Logs
                            </a>
                        @endif

                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('dean'))
                            <a href="{{ route('goal.index') }}"
                                class="{{ request()->routeIs('goal.index') ? 'active' : '' }}">
                                <i class="bx bxs-bullseye text-lg"></i>
                                College Goals
                            </a>
                        @endif

                        {{-- @if(auth()->user()->hasRole('dean') || auth()->user()->hasRole('chair'))
                            <a href="{{ route('organizational.hierarchy') }}"
                                class="{{ request()->routeIs('organizational.hierarchy') ? 'active' : '' }}">
                                <i class="bx bxs-id-card text-lg"></i>
                                Role
                            </a>
                        @endif --}}

                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('chair'))
                            <a href="{{ route('objective.index') }}"
                                class="{{ request()->routeIs('objective.index') ? 'active' : '' }}">
                                <i class="bx bx-list-check text-lg"></i>
                                Department Objectives
                            </a>
                            <a href="{{ route('programs.index') }}"
                                    class="{{ request()->routeIs('programs.index') ? 'active' : '' }}">
                                <i class="bx bxs-network-chart text-lg"></i>
                                PEOs & POs
                            </a>
                            <a href="{{ route('courses.index') }}"
                                class="{{ request()->routeIs('courses.index') ? 'active' : '' }}">
                                <i class="bx bxs-book-content text-lg"></i>
                                Courses
                            </a>
                        @endif

                    @endauth

                    @guest
                        <a href="{{ route('auth.show') }}" class="flex items-center gap-3 rounded-xl px-3 py-2 text-slate-700 hover:bg-cyan-50 hover:text-cyan-800 transition">
                            <i class="bx bxs-log-in-circle text-lg"></i>
                            Login / Register
                        </a>
                    @endguest
                </nav>

                @auth
                    <div class="pt-4 px-4 border-t border-slate-200">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <x-button type="submit" variant="danger" class="w-full text-center">
                                <i class="bx bxs-log-out text-lg"></i>
                                Logout
                            </x-button>
                        </form>
                    </div>
                @endauth
            </div>
        </aside>

        <div class="flex flex-col min-h-screen {{ $isWizardRoute ? '' : 'lg:ml-72' }}">
            <header class="sticky top-0 z-20 green-grad backdrop-blur border-b border-slate-200">
                <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <button id="sidebar-open" class="lg:hidden {{ $isWizardRoute ? 'hidden' : 'inline-flex' }} items-center justify-center h-10 w-10 rounded-full border border-slate-200 text-slate-700 hover:bg-slate-100 transition">
                            <i class="bx bx-menu text-2xl"></i>
                        </button>
                        <div>
                            <div>
                                <p class="text-xs uppercase tracking-[0.3em] font-semibold text-amber-400">Management System</p>
                                <h1 class="brand-title text-2xl mt-1 text-white">Workspace</h1>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('profile.index') }}" class="flex items-center gap-3 rounded-2xl bg-white/10 backdrop-blur px-3 py-2 hover:bg-white/20 transition cursor-pointer">
                        <div class="h-9 w-9 rounded-full bg-amber-400 text-white flex items-center justify-center">
                            <i class="bx bxs-user text-lg"></i>
                        </div>

                        <div class="hidden sm:block text-sm leading-tight">
                            <p class="font-semibold text-white">
                                {{ Auth::user()->name ?? 'User' }}
                            </p>
                        </div>
                    </a>
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

        if (openBtn) {
            openBtn.addEventListener('click', openSidebar);
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', closeSidebar);
        }

        if (overlay) {
            overlay.addEventListener('click', closeSidebar);
        }

        window.addEventListener('resize', () => {
            if (!sidebar || !overlay) {
                return;
            }

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
