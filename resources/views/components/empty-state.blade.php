@props([
    'icon'    => 'bx-inbox',
    'title'   => 'Nothing here yet',
    'message' => 'There is nothing to show right now.',
])

{{--
x-empty-state
─────────────────────────────────────────────────────────────────────
Full-width dashed-border empty state card. Use when a list, table, or
section panel has no records to display.

For empty rows inside a <table>, use <x-table.empty> instead —
this component is for outside-of-table usage only.

USAGE:

  Basic (uses default icon/title/message):
    <x-empty-state />

  Custom text:
    <x-empty-state
        icon="bx-target-lock"
        title="No goals yet"
        message="Select a college, then add the first goal using the form." />

  With a call-to-action button in the slot:
    <x-empty-state
        icon="bx-book-add"
        title="No courses found"
        message="This program has no courses yet.">
        <x-button href="{{ route('courses.create') }}" variant="add-button">
            <i class="bx bx-plus"></i> Add Course
        </x-button>
    </x-empty-state>

  Inside a @forelse / @empty block:
    @forelse ($goals as $goal)
    @empty
        <x-empty-state
            icon="bx-target-lock"
            title="No goals"
            message="No goals have been set for this college yet." />
    @endforelse

  NOTE: Do NOT nest inside <tbody> — place outside the table.
        For in-table empty rows, use: <x-table.empty :colspan="N" message="…" />
--}}

<div {{ $attributes->class([
    'rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/80 p-8 sm:p-10 text-center'
]) }}>
    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full text-slate-300">
        <i class="bx {{ $icon }} text-4xl leading-none"></i>
    </div>

    <h3 class="text-sm sm:text-base font-semibold text-slate-500">{{ $title }}</h3>
    <p class="mt-1.5 text-sm text-slate-400 max-w-sm mx-auto leading-relaxed">{{ $message }}</p>

    {{-- Only renders the slot wrapper when slot content is provided --}}
    @if ($slot->isNotEmpty())
        <div class="mt-5 flex justify-center gap-2 flex-wrap">
            {{ $slot }}
        </div>
    @endif
</div>
