@extends('layouts.app')

@section('content')

    <x-page-header
        icon="bx-buildings"
        title="College Dean Management"
        desc="Manage institutional leadership and organizational structure across all colleges" />

    <x-panel>

        @if ($colleges->isEmpty())
            <x-empty-state
                icon="bxs-building"
                title="No colleges found"
                message="Please create colleges first before managing deans and departments." />
        @else
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">

                @foreach ($colleges as $college)
                    @php $dean = $deanAssignments->get($college->id)?->first()?->user; @endphp

                    <div class="flex flex-col rounded-xl border border-[#e2e8f0] bg-white overflow-hidden" style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">

                        {{-- Card header --}}
                        <div class="flex items-center gap-3 px-5 py-4 bg-[#f8fafc] border-b border-[#e2e8f0]">
                            <span class="shrink-0 w-9 h-9 rounded-lg bg-[#16a34a] flex items-center justify-center">
                                <i class="bx bxs-school text-white text-lg leading-none"></i>
                            </span>
                            <div class="flex-1 min-w-0">
                                <h2 class="text-[13px] font-bold text-[#0f172a] truncate" title="{{ $college->name }}">
                                    {{ $college->name }}
                                </h2>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    {{ $college->departments->count() }} department{{ $college->departments->count() !== 1 ? 's' : '' }}
                                </p>
                            </div>
                        </div>

                        {{-- Body --}}
                        <div class="flex flex-col gap-4 p-4 flex-1">

                            {{-- Dean section --}}
                            <x-card title="College Dean" icon="user" color="slate" :shadow="false">
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
                                        <form action="{{ route('organizational.remove-dean') }}" method="POST" class="shrink-0">
                                            @csrf
                                            <input type="hidden" name="college_id" value="{{ $college->id }}">
                                            <input type="hidden" name="user_id" value="{{ $dean->id }}">
                                            <button type="submit"
                                                class="p-1.5 rounded-lg text-rose-400 hover:bg-rose-50 hover:text-rose-600 transition-colors"
                                                title="Remove dean">
                                                <i class="bx bx-trash text-base leading-none"></i>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <p class="text-xs text-slate-400 italic">No dean assigned yet.</p>
                                @endif
                            </x-card>

                            {{-- Actions --}}
                            <div class="mt-auto pt-3 border-t border-slate-100 flex flex-col gap-2">

                                <x-button
                                    href="{{ route('organizational.departments.index', $college->id) }}"
                                    variant="add-button"
                                    class="w-full justify-center">
                                    <i class="bx bx-building"></i>
                                    Manage Departments ({{ $college->departments->count() }})
                                </x-button>

                                @if (!$dean && $potentialDeans->count() > 0)
                                    <x-button
                                        onclick="document.getElementById('assignDeanModal-{{ $college->id }}').showModal()"
                                        variant="cancel"
                                        class="w-full justify-center">
                                        <i class="bx bx-user-plus"></i>
                                        Assign Dean
                                    </x-button>
                                @elseif (!$dean)
                                    <p class="text-center text-xs text-slate-400 py-1">No available users to assign</p>
                                @endif

                            </div>
                        </div>
                    </div>

                    @include('OrganizationalHierarchy.modals.assignDeanModal', [
                        'collegeId'      => $college->id,
                        'collegeName'    => $college->name,
                        'potentialDeans' => $potentialDeans,
                    ])
                @endforeach

            </div>
        @endif

    </x-panel>

@endsection
