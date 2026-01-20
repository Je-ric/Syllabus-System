<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Syllabus System' }}</title>

    <script src="https://cdn.tailwindcss.com"></script>

    @livewireStyles
</head>

<body class="min-h-screen">

    <header class="bg-blue-700 text-white">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="font-bold text-xl">Syllabus System</h1>
            <nav class="space-x-4 text-sm">
                <a href="{{ route('accounts.approval') }}" class="hover:underline">User Management</a>
                
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
