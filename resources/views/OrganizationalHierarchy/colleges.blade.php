@extends('layouts.app')

@section('content')

    <x-page-header
        icon="bx-buildings"
        title="CLSU College Dean Management"
        desc="Manage institutional leadership and organizational structure across all colleges">
    </x-page-header>

    <x-panel>
        {{-- No Colleges --}}
        @if ($colleges->isEmpty())
            <div class="border border-emerald-200 rounded-xl p-12 text-center bg-linear-to-br from-emerald-50 to-green-50 shadow-sm">
                <div class="flex justify-center mb-4">
                    <i class="bx bxs-building text-5xl text-emerald-300"></i>
                </div>
                <p class="text-emerald-800 font-semibold text-lg mb-2">
                    No colleges found
                </p>
                <p class="text-emerald-700 text-sm">
                    Please create colleges first before managing deans and departments.
                </p>
            </div>
        @else
            {{-- COLLEGE GRID --}}
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
    
                @foreach ($colleges as $college)
                    <div class="border border-emerald-200 rounded-xl bg-white shadow-sm hover:shadow-md transition-shadow overflow-hidden flex flex-col h-full">
    
                        {{-- College Header --}}
                        <div class="green-grad px-6 py-5 flex items-center gap-4">
                            <div class="shrink-0 w-11 h-11 rounded-lg
                                bg-yellow-500
                                flex items-center justify-center shadow-md">
                                <i class="bx bxs-school text-white text-lg font-bold"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h2 class="text-base font-bold text-white truncate" title="{{ $college->name }}">
                                    {{ $college->name }}
                                </h2>
                            </div>
                        </div>
    
                        {{-- Card Body --}}
                        <div class="p-6 flex flex-col gap-5 flex-1">
    
                            {{-- Current Dean --}}
                            @if ($deanAssignments->get($college->id)?->first())
                                <div class="border border-emerald-200 rounded-lg p-4 bg-linear-to-br from-emerald-50 to-green-50 hover:from-emerald-100 hover:to-green-100 transition-colors">
                                    <p class="text-xs uppercase tracking-[0.2em] text-emerald-800 font-semibold flex items-center gap-2 mb-3">
                                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-emerald-700 text-white text-xs">
                                            <i class="bx bxs-user-badge text-xs"></i>
                                        </span>
                                        College Dean
                                    </p>
    
                                    @if ($deanAssignments->get($college->id)?->first())
                                        @php
                                            $dean = $deanAssignments->get($college->id)->first()->user;
                                        @endphp
    
                                        <div class="flex justify-between items-start gap-4">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-semibold text-emerald-900 leading-tight truncate" title="{{ $dean->name }}">
                                                    {{ $dean->name }}
                                                </p>
                                                <p class="text-xs text-emerald-700 mt-1.5 truncate" title="{{ $dean->email }}">
                                                    {{ $dean->email }}
                                                </p>
                                            </div>
    
                                            <form action="{{ route('organizational.remove-dean') }}" method="POST" class="shrink-0">
                                                @csrf
                                                <input type="hidden" name="college_id" value="{{ $college->id }}">
                                                <input type="hidden" name="user_id" value="{{ $dean->id }}">
    
                                                <button type="submit"
                                                    class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition flex items-center gap-1 text-sm"
                                                    title="Remove dean">
                                                    <i class="bx bx-trash text-base"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <p class="text-xs text-emerald-700 mt-2">No dean assigned yet.</p>
                                    @endif
                                </div>
                            @else
                                <div class="border border-dashed border-emerald-300 rounded-lg p-5 text-center bg-emerald-50 hover:bg-green-50 transition-colors">
                                    <i class="bx bx-user text-2xl text-emerald-400 mb-2 block"></i>
                                    <p class="text-emerald-700 text-sm font-medium">
                                        No dean assigned
                                    </p>
                                </div>
                            @endif
    
                            {{-- ACTIONS --}}
                            <div class="mt-auto space-y-2.5 pt-3 border-t border-slate-100">
    
                                <x-button href="{{ route('organizational.departments.index', $college->id) }}"
                                    variant="secondary" class="w-full justify-center text-sm font-medium">
                                    <i class="bx bx-building mr-2"></i>
                                    Manage Departments  ({{ $college->departments->count() }})
                                </x-button>
    
                                @if (!$deanAssignments->get($college->id)?->first() && $potentialDeans->count() > 0)
                                    <x-button
                                        onclick="document.getElementById('assignDeanModal-{{ $college->id }}').showModal()"
                                        variant="secondary" class="w-full justify-center text-sm font-medium">
                                        <i class="bx bx-user-plus mr-2"></i>
                                        Assign Dean
                                    </x-button>
                                @elseif (!$deanAssignments->get($college->id)?->first())
                                    <div class="w-full p-3 text-center text-xs text-slate-500 bg-slate-100 rounded-lg font-medium">
                                        No available users to assign
                                    </div>
                                @endif
    
                            </div>
    
                        </div>
                    </div>
    
                    {{-- Modal --}}
                    @include('OrganizationalHierarchy.modals.assignDeanModal', [
                        'collegeId' => $college->id,
                        'collegeName' => $college->name,
                        'potentialDeans' => $potentialDeans,
                    ])
                @endforeach
    
            </div>
        @endif
    </x-panel>

@endsection
