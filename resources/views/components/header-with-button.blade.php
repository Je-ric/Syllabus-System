@props([
    'title',
    'description' => null,
])

{{--
x-header-with-button
─────────────────────────────────────────────────────────────────────
Page-section header with CLSU gold bottom border.
The slot accepts any content (buttons, links, chips, etc.).
The slot wrapper div is only rendered when slot content exists.

USAGE:
  Title only:
    <x-header-with-button title="Dashboard" />

  With description:
    <x-header-with-button
        title="User Management"
        description="Manage accounts and permissions" />

  With one action button:
    <x-header-with-button title="Courses" description="View and manage program courses">
        <x-button href="{{ route('courses.create') }}" variant="add-button">
            <i class="bx bx-plus"></i> Add Course
        </x-button>
    </x-header-with-button>

  With multiple slot items (back + save):
    <x-header-with-button title="Edit Course">
        <x-button href="{{ route('courses.index') }}" variant="cancel">
            <i class="bx bx-arrow-back"></i> Back
        </x-button>
        <x-button type="submit" variant="save">
            <i class="bx bx-save"></i> Save
        </x-button>
    </x-header-with-button>
--}}

<div class="w-full flex flex-col sm:flex-row justify-between items-start sm:items-center
            gap-4 mb-5 pb-4 border-b-4 border-yellow-400">

    <div class="flex flex-col min-w-0">
        {{-- slate-800 for body text — green-* is reserved for CLSU brand accents --}}
        <h1 class="text-slate-800 text-xl sm:text-2xl font-bold tracking-tight leading-tight">
            {{ $title }}
        </h1>

        @if ($description)
            <p class="mt-0.5 text-slate-500 text-sm sm:text-base leading-relaxed font-normal">
                {{ $description }}
            </p>
        @endif
    </div>

    {{-- Only renders if slot has content — avoids an empty spacer div --}}
    @if ($slot->isNotEmpty())
        <div class="flex flex-wrap items-center gap-2 shrink-0">
            {{ $slot }}
        </div>
    @endif
</div>
