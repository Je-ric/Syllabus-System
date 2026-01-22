<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Syllabus System' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    @livewireStyles
</head>

<body class="min-h-screen">

    <header class="bg-blue-700 text-white">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="font-bold text-xl">Syllabus System</h1>
            <nav class="space-x-4 text-sm">
                <a href="{{ route('accounts.approval') }}" class="hover:underline">User Management</a>
                <a href="{{ route('academic.structure.index') }}" class="hover:underline">Academic Structure</a>
                {{-- <a href="{{ route('goal.index') }}" class="hover:underline">College Goals</a>
                <a href="{{ route('objective.index') }}" class="hover:underline">Department Objectives</a> --}}
                @auth
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="hover:underline bg-transparent border-0 p-0 cursor-pointer">
                            Logout
                        </button>
                    </form>
                @endauth
            </nav>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-6">
        @yield('content')
    </main>

    @livewireScripts
</body>

</html>
