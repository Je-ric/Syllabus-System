@extends('layouts.app')

@section('content')

    <x-layout.page-header
        icon="bx-buildings"
        title="College Dean Management"
        desc="Manage institutional leadership and organizational structure across all colleges">
        <x-ui.help-trigger />
    </x-layout.page-header>

    <x-layout.help-panel module="user-assignments" />

    <script>
        function filterColleges(searchTerm) {
            const term = searchTerm.toLowerCase();
            const cards = document.querySelectorAll('[data-college-name]');
            
            cards.forEach(card => {
                const name = card.getAttribute('data-college-name').toLowerCase();
                const dean = card.getAttribute('data-dean-name')?.toLowerCase() || '';
                
                if (name.includes(term) || dean.includes(term)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>

    <x-layout.panel>

        {{-- Search and Filter --}}
        @if (!$colleges->isEmpty())
            <div class="mb-4">
                <div class="relative">
                    <input
                        type="text"
                        id="collegeSearch"
                        placeholder="Search colleges..."
                        class="w-full pl-10 pr-4 py-2.5 text-sm border border-[#e2e8f0] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#00c075] focus:border-transparent"
                        oninput="filterColleges(this.value)"
                    >
                    <i class="bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                </div>
            </div>
        @endif

        @if ($colleges->isEmpty())
            <x-feedback-status.empty-state
                icon="bxs-building"
                title="No colleges found"
                message="Please create colleges first before managing deans and departments." />
        @else
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">

                @foreach ($colleges as $college)
                    @php $dean = $deanAssignments->get($college->id)?->first()?->user; @endphp

                    <x-layout.card-section title="{{ $college->name }}"
                                    icon="bx bxs-school"
                                    :padded="false"
                                    data-college-name="{{ $college->name }}"
                                    data-dean-name="{{ $dean?->name ?? '' }}">

                        {{-- Body --}}
                        <div class="flex flex-col gap-4 p-4 flex-1">

                            {{-- Dean section --}}
                            <x-layout.card title="College Dean" icon="user" color="slate" :shadow="false">
                                <x-slot name="action">
                                    <div class="group relative">
                                        <button type="button" class="text-slate-400 hover:text-slate-600 transition-colors">
                                            <i class="bx bx-info-circle text-sm"></i>
                                        </button>
                                        <div class="absolute right-0 top-full mt-2 w-64 p-3 bg-slate-800 text-white text-xs rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-10">
                                            <p class="font-semibold mb-1">College Dean</p>
                                            <p class="text-slate-300">Manages all departments within a college. Oversees academic and administrative operations at the college level.</p>
                                        </div>
                                    </div>
                                </x-slot>
                                @if ($dean)
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-slate-800 truncate" title="{{ $dean->name }}">
                                                {{ $dean->name }}
                                            </p>
                                            <p class="text-xs text-slate-500 mt-0.5 truncate" title="{{ $dean->email }}">
                                                {{ $dean->email }}
                                            </p>
                                        </div>
                                        <button type="button"
                                            onclick="document.getElementById('removeDeanModal-{{ $college->id }}').showModal()"
                                            class="p-1.5 rounded-lg text-rose-400 hover:bg-rose-50 hover:text-rose-600 transition-colors"
                                            title="Remove dean">
                                            <i class="bx bx-trash text-base leading-none"></i>
                                        </button>
                                    </div>
                                @else
                                    <p class="text-xs text-slate-400 italic">No dean assigned yet.</p>
                                @endif
                            </x-layout.card>

                            {{-- Actions --}}
                            <div class="mt-auto pt-3 border-t border-slate-100 flex flex-col gap-2">

                                <x-ui.button
                                    href="{{ route('user-assignments.departments.index', $college->id) }}"
                                    variant="add-button"
                                    class="w-full justify-center">
                                    <i class="bx bx-building"></i>
                                    Manage Departments ({{ $college->departments->count() }})
                                </x-ui.button>

                                @if (!$dean && $potentialDeans->count() > 0)
                                    <x-ui.button
                                        onclick="document.getElementById('assignDeanModal-{{ $college->id }}').showModal()"
                                        variant="cancel"
                                        class="w-full justify-center">
                                        <i class="bx bx-user-plus"></i>
                                        Assign Dean
                                    </x-ui.button>
                                @elseif (!$dean)
                                    <p class="text-center text-xs text-slate-400 py-1">No available users to assign</p>
                                @endif

                            </div>
                        </div>
                    </x-layout.card-section>

                    @include('UserManagement.UserAssignments.modals.assignDeanModal', [
                        'collegeId'      => $college->id,
                        'collegeName'    => $college->name,
                        'potentialDeans' => $potentialDeans,
                    ])

                    @if ($dean)
                        @include('UserManagement.UserAssignments.modals.removeDeanModal', [
                            'collegeId'   => $college->id,
                            'collegeName' => $college->name,
                            'userId'      => $dean->id,
                            'userName'    => $dean->name,
                        ])
                    @endif
                @endforeach

            </div>
        @endif

    </x-layout.panel>

@endsection
