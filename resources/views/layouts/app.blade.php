<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Syllabus System' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Libre+Franklin:ital,wght@0,100..900;1,100..900&family=Oswald:wght@200..700&family=Source+Sans+3:ital,wght@0,200..900;1,200..900&display=swap" rel="stylesheet">
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --brand-ink: #0b1220;
            --brand-cyan: #0e7490;
            --brand-cyan-dark: #0b556b;
            --panel: #ffffff;
            --panel-muted: #f1f5f9;

            --scrollbar-thumb: #ffb51b;
            --scrollbar-thumb-hover: #e6a700;
            --scrollbar-track: #f1f1f1;

            --red: #e2231a;
            --red-100: #c01e16;
            --blue-50: #dde5eb;
            --blue: #008dea;
            --orange: #f9c150;
            --white: #ffffff;
            --white-50: #f1f2f3;
            --gray: #dad8d8;
            --gray-100: #979797;
            --black-20: #525252;
            --black-25: #3a3a3a;
            --black-50: #313131;
            --yellow: #cdfb13;
            --clsu-yellow: #ffd700;
            --clsu-gold: #e0a70d;
            --clsu-cobra: #1a5f30;
            --clsu-green: #009639;
            --green: #92d12c;
            --green-50: #4eab18;
            --green-100: #038303;
            --green-150: #003a10;
            --green-grad: linear-gradient(90deg, rgba(0, 129, 2, 1) 0%, rgba(149, 210, 45, 1) 100%);
        }

        body {
            font-family: "Source Sans 3", "Libre Franklin", system-ui, -apple-system, sans-serif;
            background: radial-gradient(1100px 600px at 15% -10%, #e0f2fe 0%, #f8fafc 45%, #f1f5f9 100%);
            color: var(--brand-ink);
        }

        .green-grad {
            background: var(--green-grad);
        }

        .brand-title {
            font-family: "Anton", "Oswald", sans-serif;
            letter-spacing: 0.04em;
        }

        aside a {
            display: flex;
            align-items: center;
            gap: 0.75rem; 
            padding: 0.5rem 0.75rem;
            color: #374151; 
            text-decoration: none;
            font-weight: 500;
            border-right: 5px solid transparent;
            transition: all 0.3s ease;
            background: white;
        }

        /* Hover effect */
        aside a:hover {
            color: var(--clsu-green); 
            border-right-color: var(--clsu-green);
            transition: all 0.3s ease;
            background: linear-gradient(
                to left,
                rgba(0, 150, 57, 0.25) 0%,
                rgba(0, 150, 57, 0.12) 30%,
                rgba(0, 150, 57, 0.05) 60%,
                white 100%
            );
        }

        aside a.active{
            font-weight: 700;
            color: var(--clsu-green);
            border-right: 5px solid var(--clsu-green);
            background: linear-gradient(
                to left,
                rgba(0, 150, 57, 0.25) 0%,
                rgba(0, 150, 57, 0.12) 30%,
                rgba(0, 150, 57, 0.05) 60%,
                white 100%
            );
        }
        
    </style>
</head>

<body class="min-h-screen">

    @if (session('toast'))
        <x-feedback-status.toast :message="session('toast')['message']" :type="session('toast')['type']" />
    @endif

    <div class="min-h-screen">
        <div id="sidebar-overlay" class="fixed inset-0 bg-green-900/50 backdrop-blur-sm z-30 hidden lg:hidden"></div>

        <aside id="app-sidebar"
                class="fixed inset-y-0 left-0 z-40 w-72
                    bg-white shadow-2xl border-r border-slate-200
                    transform -translate-x-full lg:translate-x-0
                    transition-transform duration-300 ease-out
                    h-full overflow-y-auto no-scrollbar">
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
                        @endif

                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('dean'))
                            <a href="{{ route('goal.index') }}"
                                class="{{ request()->routeIs('goal.index') ? 'active' : '' }}">
                                <i class="bx bxs-bullseye text-lg"></i>
                                College Goals
                            </a>
                        @endif

                        @if(auth()->user()->hasRole('dean') || auth()->user()->hasRole('chair'))
                            <a href="{{ route('organizational.hierarchy') }}"
                                class="{{ request()->routeIs('organizational.hierarchy') ? 'active' : '' }}">
                                <i class="bx bxs-id-card text-lg"></i>
                                Role
                            </a>
                        @endif

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
                                Manage Courses
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

        <div class="flex flex-col min-h-screen lg:ml-72">
            <header class="sticky top-0 z-20 green-grad backdrop-blur border-b border-slate-200">
                <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <button id="sidebar-open" class="lg:hidden inline-flex items-center justify-center h-10 w-10 rounded-full border border-slate-200 text-slate-700 hover:bg-slate-100 transition">
                            <i class="bx bx-menu text-2xl"></i>
                        </button>
                        <div>
                            <div>
                                <p class="text-xs uppercase tracking-[0.3em] font-semibold text-amber-400">Management System</p>
                                <h1 class="brand-title text-2xl mt-1 text-white">Workspace</h1>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 rounded-2xl bg-white/10 backdrop-blur px-3 py-2 hover:bg-white/20 transition cursor-pointer">
                        <div class="h-9 w-9 rounded-full bg-amber-400 text-white flex items-center justify-center">
                            <i class="bx bxs-user text-lg"></i>
                        </div>

                        <div class="hidden sm:block text-sm leading-tight">
                            <p class="font-semibold text-white">
                                {{ Auth::user()->name ?? 'User' }}
                            </p>
                        </div>

                        <i class="bx bx-chevron-down text-white/70"></i>
                    </div>
                </div>
            </header>

            <main class="flex-1 mx-auto w-full px-4 py-4 overflow-y-auto">
                <div class="bg-white shadow-lg rounded-3xl border border-slate-100 p-6 sm:p-8">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @livewireScripts
    <script>
        const sidebar = document.getElementById('app-sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const openBtn = document.getElementById('sidebar-open');
        const closeBtn = document.getElementById('sidebar-close');

        const openSidebar = () => {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        };

        const closeSidebar = () => {
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
