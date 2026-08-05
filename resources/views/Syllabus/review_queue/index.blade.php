@extends('layouts.app')

@section('content')

<x-layout.page-header
    icon="bx-clipboard-check"
    title="Review Queue"
    desc="Syllabi assigned to you for CQI review">
</x-layout.page-header>

@php
    $tabs = [
        ['id' => 'pending', 'label' => 'Needs Review',  'count' => $pending->count()],
        ['id' => 'done',    'label' => 'Reviewed',       'count' => $done->count()],
    ];
@endphp

<x-layout.panel>
    <x-navigation.tabs-modern
        :tabs="$tabs"
        :defaultTab="'pending'"
        :stateKey="'review-queue'">

        {{-- ── Needs Review tab ─────────────────────────────────── --}}
        <x-slot name="slot_pending">
            @if ($pending->isEmpty())
                <x-feedback-status.empty-state
                    icon="bx-check-shield"
                    title="All clear — nothing to review"
                    message="You have no syllabi pending review at the moment." />
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach ($pending as $assignment)
                        @include('Syllabus.review_queue.review-queue-card', [
                            'assignment' => $assignment,
                            'syllabus'   => $assignment->syllabus,
                        ])
                    @endforeach
                </div>
            @endif
        </x-slot>

        {{-- ── Reviewed tab ─────────────────────────────────────── --}}
        <x-slot name="slot_done">
            @if ($done->isEmpty())
                <x-feedback-status.empty-state
                    icon="bx-history"
                    title="No completed reviews yet"
                    message="Syllabi you have already reviewed will appear here." />
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach ($done as $assignment)
                        @include('Syllabus.review_queue.review-queue-card', [
                            'assignment' => $assignment,
                            'syllabus'   => $assignment->syllabus,
                        ])
                    @endforeach
                </div>
            @endif
        </x-slot>

    </x-navigation.tabs-modern>
</x-layout.panel>

@endsection
