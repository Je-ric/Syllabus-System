<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Syllabus System' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen">

    @if (session('toast'))
        <x-feedback-status.toast :message="session('toast')['message']" :type="session('toast')['type']" />
    @endif

    <header class="bg-black text-white">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="font-bold text-xl">Syllabus System</h1>
            <nav class="space-x-4 text-sm">

                @auth
                    {{-- Admin-only links --}}
                    @if(auth()->user()->hasRole('admin'))
                        <a href="{{ route('accounts.approval') }}" class="hover:underline">User Management</a>
                        <a href="{{ route('organizational.colleges.index') }}" class="hover:underline">Organizational Hierarchy</a>
                        <a href="{{ route('academic.structure.index') }}" class="hover:underline">Academic Structure</a>
                        <a href="{{ route('academic.calendars.index') }}" class="hover:underline">Academic Calendars</a>
                    @endif

                    {{-- Admin + Dean --}}
                    @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('dean'))
                    <a href="{{ route('goal.index') }}" class="hover:underline">College Goals</a>
                    @endif

                    {{-- Admin + Dean + Chair --}}
                    @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('chair'))
                        <a href="{{ route('objective.index') }}" class="hover:underline">Department Objectives</a>
                        <a href="{{ route('programs.index') }}" class="hover:underline">PEOs & POs</a>
                        <a href="{{ route('courses.index') }}" class="hover:underline">Manage Courses</a>
                    @endif

                    @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('faculty'))
                        <a href="{{ route('syllabus.index') }}" class="hover:underline">Syllabi</a>
                    @endif

                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="hover:underline bg-transparent border-0 p-0 cursor-pointer">
                            Logout
                        </button>
                    </form>
                @endauth

                @guest
                    <a href="{{ route('auth.show') }}" class="hover:underline">Login / Register</a>
                @endguest

            </nav>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-6">
        @yield('content')
    </main>

    @livewireScripts
</body>

</html>
