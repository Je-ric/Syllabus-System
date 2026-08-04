{{-- Layout partial: fixed left sidebar — brand, role-gated nav, user card --}}

<aside id="app-sidebar"
    class="fixed inset-y-0 left-0 z-40 w-60
           bg-white flex flex-col
           border-r border-[#E3E8EB]
           transform -translate-x-full lg:translate-x-0
           transition-transform duration-300 ease-out
           {{ $isWizardRoute ? 'hidden lg:hidden' : '' }}"
    style="box-shadow: 1px 0 0 rgba(16,24,40,0.02), 4px 0 24px -8px rgba(16,24,40,0.06);">

    {{-- Brand --}}
    <div class="shrink-0 px-4 pt-5 pb-4 border-b border-[#F1F3F5]">
        <div class="flex items-center justify-between">
            <x-ui.brand-logo />
            <button id="sidebar-close"
                class="lg:hidden text-[#A5B2BD] hover:text-[#394056] transition p-1.5 rounded-[8px] hover:bg-[#F1F3F5]"
                aria-label="Close navigation">
                <i class="bx bx-x text-xl"></i>
            </button>
        </div>
    </div>

    {{-- Nav --}}
    <div class="flex-1 overflow-y-auto no-scrollbar py-3 px-2.5 space-y-5">
        @auth
            <nav aria-label="Main">
                <p class="nav-label">Home</p>
                <x-ui.nav-link href="{{ route('dashboard') }}" icon="bx-grid-alt" :active="request()->routeIs('dashboard')">
                    Dashboard
                </x-ui.nav-link>
            </nav>

            @if ($user->hasRole('admin') || $user->hasRole('faculty') || $user->hasRole('ovpaa'))
                <nav aria-label="Syllabus">
                    <p class="nav-label">Syllabus</p>
                    <x-ui.nav-link href="{{ route('syllabus.index') }}" icon="bxs-notepad" :active="request()->routeIs('syllabus.index')">
                        Syllabi
                    </x-ui.nav-link>
                    @if ($user->hasRole('admin') || $user->hasRole('faculty'))
                        <x-ui.nav-link href="{{ route('workload.index') }}" icon="bx-briefcase" :active="request()->routeIs('workload.*')">
                            My Workload
                        </x-ui.nav-link>
                    @endif
                </nav>
            @endif

            {{-- Review Queue — chairs assigned as reviewers + faculty reviewers + admin --}}
            @if ($user->hasRole('admin') || $user->hasRole('chair') || $user->hasRole('faculty'))
                <nav aria-label="Review">
                    <p class="nav-label">CQI Review</p>
                    <x-ui.nav-link href="{{ route('syllabus.review-queue.index') }}" icon="bx-clipboard-check" :active="request()->routeIs('syllabus.review-queue.*')">
                        Review Queue
                    </x-ui.nav-link>
                </nav>
            @endif

            @if ($user->hasRole('admin') || $user->hasRole('chair') || $user->hasRole('dean') || $user->hasRole('ovpaa'))
                <nav aria-label="Academic Setup">
                    <p class="nav-label">Academic Setup</p>
                    @if ($user->hasRole('admin') || $user->hasRole('ovpaa'))
                        <x-ui.nav-link href="{{ route('academic.calendars.index') }}" icon="bxs-calendar"
                            :active="request()->routeIs('academic.calendars.*', 'academic.calendar.*')">
                            Academic Calendars
                        </x-ui.nav-link>
                    @endif
                    @if ($user->hasRole('admin') || $user->hasRole('dean'))
                        <x-ui.nav-link href="{{ route('goal.index') }}" icon="bxs-bullseye" :active="request()->routeIs('goal.*')">
                            College Goals
                        </x-ui.nav-link>
                    @endif
                    @if ($user->hasRole('admin') || $user->hasRole('chair'))
                        <x-ui.nav-link href="{{ route('objective.index') }}" icon="bx-list-check" :active="request()->routeIs('objective.*')">
                            Department Objectives
                        </x-ui.nav-link>
                        <x-ui.nav-link href="{{ route('programs.index') }}" icon="bxs-network-chart" :active="request()->routeIs('programs.*')">
                            PEOs &amp; POs
                        </x-ui.nav-link>
                        <x-ui.nav-link href="{{ route('courses.index') }}" icon="bxs-book-content" :active="request()->routeIs('courses.*')">
                            Courses
                        </x-ui.nav-link>
                    @endif
                </nav>
            @endif

            @if ($user->hasRole('admin'))
                <nav aria-label="Administration">
                    <p class="nav-label">Administration</p>
                    <x-ui.nav-link href="{{ route('user-assignments.colleges.index') }}" icon="bx-sitemap" :active="request()->routeIs('user-assignments.*')">
                        University Faculties
                    </x-ui.nav-link>
                    <x-ui.nav-link href="{{ route('university.structure.index') }}" icon="bxs-layer" :active="request()->routeIs('university.structure.*')">
                        Academic Structure
                    </x-ui.nav-link>
                    <x-ui.nav-link href="{{ route('accounts.approval') }}" icon="bxs-user-detail" :active="request()->routeIs('accounts.approval')">
                        User Management
                    </x-ui.nav-link>
                    <x-ui.nav-link href="{{ route('audit.logs.index') }}" icon="bx-history" :active="request()->routeIs('audit.logs.*')">
                        Audit Logs
                    </x-ui.nav-link>
                </nav>
            @endif
        @endauth

        @guest
            <nav aria-label="Account">
                <x-ui.nav-link href="{{ route('auth.show') }}" icon="bxs-log-in-circle">
                    Login / Register
                </x-ui.nav-link>
            </nav>
        @endguest
    </div>

    {{-- User card --}}
    @auth
        <div class="shrink-0 border-t border-[#F1F3F5] p-3.5">
            <div class="flex items-center gap-2.5 p-2 rounded-[10px] bg-[#FAFDFB] border border-[#F1F3F5]">
                <x-ui.user-avatar :name="$user->name" size="md" />
                <div class="min-w-0 flex-1">
                    <p class="text-[13px] font-semibold text-[#09090b] truncate">{{ $user->name ?? 'User' }}</p>
                    <p class="text-[11px] text-[#71717a] capitalize">{{ $user->roles->first()?->name ?? 'Member' }}</p>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Sign out"
                        class="flex items-center justify-center w-8 h-8 rounded-[8px]
                               text-[#A5B2BD] hover:text-[#e11d48] hover:bg-[#fff1f2]
                               transition-colors duration-150">
                        <i class="bx bxs-log-out text-base"></i>
                    </button>
                </form>
            </div>
        </div>
    @endauth
</aside>
