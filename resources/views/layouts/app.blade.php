<!DOCTYPE html>
<html lang="en">
<head>
    @include('includes.head-assets')
    <style>
    [x-cloak] {
        display: none !important;
    }
    </style>
</head>

<body class="min-h-screen">

    @php
        $isWizardRoute   = request()->routeIs('syllabus.wizard');
        $isReviewerRoute = request()->routeIs('syllabus.reviewer.show');
        $isFullscreen    = $isWizardRoute || $isReviewerRoute;
        $user            = Auth::user();
    @endphp

    {{-- Toast notifications --}}
    @if (session('toast'))
        <x-feedback-status.toast :message="session('toast')['message']" :type="session('toast')['type']" />
    @endif
    <x-feedback-status.toast />

    {{-- Mobile sidebar overlay --}}
    <div id="sidebar-overlay"
        class="fixed inset-0 bg-[#09090b]/40 backdrop-blur-sm z-30 hidden lg:hidden
               {{ $isFullscreen ? 'hidden!' : '' }}">
    </div>

    @if (! $isReviewerRoute)
        @include('layouts.partials.sidebar')
    @endif

    {{-- Main content column --}}
    <div class="flex flex-col min-h-screen {{ $isFullscreen ? '' : 'lg:pl-60' }}">

        @if (! $isReviewerRoute)
            @include('layouts.partials.topbar')
        @endif

        <main class="flex-1 w-full">
            @yield('content')
        </main>

        @include('layouts.partials.footer')
    </div>

    @include('layouts.partials.session-expired-modal')

    @livewireScripts
    {{-- <script src="https://cdn.jsdelivr.net/gh/livewire/livewire@3.x.x/dist/livewire.js"></script> --}}
    @stack('scripts')

    {{-- Sidebar toggle --}}
    <script>
        const sidebar  = document.getElementById('app-sidebar');
        const overlay  = document.getElementById('sidebar-overlay');
        const openBtn  = document.getElementById('sidebar-open');
        const closeBtn = document.getElementById('sidebar-close');

        const openSidebar = () => {
            sidebar?.classList.remove('-translate-x-full');
            overlay?.classList.remove('hidden');
            openBtn?.setAttribute('aria-expanded', 'true');
        };

        const closeSidebar = () => {
            sidebar?.classList.add('-translate-x-full');
            overlay?.classList.add('hidden');
            openBtn?.setAttribute('aria-expanded', 'false');
        };

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
