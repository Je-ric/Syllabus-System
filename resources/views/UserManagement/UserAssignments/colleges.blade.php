@extends('layouts.app')

@section('content')

    <x-layout.page-header
        icon="bx-buildings"
        title="College Dean Management"
        desc="Manage institutional leadership and organizational structure across all colleges" />

    <x-layout.panel>

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
                                    :padded="false">

                        {{-- Body --}}
                        <div class="flex flex-col gap-4 p-4 flex-1">

                            {{-- Dean section --}}
                            <x-layout.card title="College Dean" icon="user" color="slate" :shadow="false">
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
