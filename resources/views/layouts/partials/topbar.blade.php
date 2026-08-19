{{-- Layout partial: sticky top header — mobile hamburger, brand text, notification bell, profile pill --}}

<header class="sticky top-0 z-20 bg-white border-b border-[#E3E8EB]
               {{ $isWizardRoute ? 'hidden' : '' }}"
        style="box-shadow: 0 1px 2px rgba(16,24,40,0.04), 0 1px 3px rgba(16,24,40,0.06);">

    <div class="px-4 sm:px-6 h-14 flex items-center justify-between gap-4">

        {{-- Left: hamburger + brand text --}}
        <div class="flex items-center gap-3 min-w-0">
            <button id="sidebar-open"
                class="lg:hidden {{ $isWizardRoute ? 'hidden' : 'inline-flex' }}
                       items-center justify-center h-8 w-8 rounded-[7px]
                       text-[#72809E] hover:text-[#394056] hover:bg-[#F1F3F5]
                       transition-all duration-150 shrink-0"
                aria-label="Open navigation"
                aria-controls="app-sidebar"
                aria-expanded="false">
                <i class="bx bx-menu text-xl"></i>
            </button>
            <span class="hidden sm:block h-5 w-px bg-[#E3E8EB] shrink-0"></span>
            <div class="hidden sm:flex flex-col justify-center min-w-0 gap-0.5">
                <p class="topbar-univ-label leading-none">
                    Central Luzon State University
                </p>
                <p class="topbar-system-name leading-none truncate">
                    Course Syllabus Management System
                </p>
            </div>
        </div>

        {{-- Right: notification bell + profile pill --}}
        @auth
            <div class="flex items-center gap-2 shrink-0">

                @include('layouts.partials.notification-bell', [
                    'initialUnreadCount' => $user
                        ? $user->unreadNotifications()->count()
                        : 0,
                ])

                {{-- Thin divider --}}
                <span class="h-5 w-px bg-[#E3E8EB] shrink-0"></span>

                {{-- Profile pill --}}
                <a href="{{ route('profile.index') }}"
                    class="flex items-center gap-2 shrink-0 rounded-full
                           bg-white border border-[#E3E8EB] pl-1.5 pr-3 py-1
                           hover:bg-[#EDFFF8] hover:border-[#00C075]
                           transition-all duration-200 group
                           {{ $isWizardRoute ? 'hidden' : '' }}"
                    aria-label="View profile">
                    <x-ui.user-avatar :name="$user->name" size="sm" />
                    <p class="hidden sm:block text-[12.5px] font-semibold text-[#394056]
                               group-hover:text-[#06754E] truncate max-w-36 leading-tight">
                        {{ $user->name ?? 'User' }}
                    </p>
                    <i class="bx bx-chevron-down text-[#C1C8D4] text-sm hidden sm:block
                               group-hover:text-[#00965F] transition-colors duration-200"></i>
                </a>

            </div>
        @endauth

    </div>
</header>
