{{--
    x-wizard.empty
    ─────────────────────────────────────────────────────────────────────────────
    Centred empty-state panel for wizard steps.

    UX principle — User-Centered Design:
      When content is absent, guide the user toward the next action rather than
      showing a plain "nothing here" message. The slot accepts a CTA button so
      the empty state is always actionable.

    Props:
      icon     string  required  — boxicons name WITHOUT "bx-" (e.g. "calendar-x")
      title    string  required  — short message e.g. "No weeks generated yet"
      message  string  optional  — one-sentence guidance
      dashed   bool    optional  — dashed border style          default: true

    Slot (optional):
      CTA button rendered below the description.

    ─── USAGE ────────────────────────────────────────────────────────────────────
    <x-wizard.empty
        icon="calendar-x"
        title="No weeks generated yet"
        message="Select an academic calendar then click Generate Weeks to begin.">

        <x-wizard.btn variant="sm-success"
            wire:click="generateWeeklyCoverage"
            :disabled="! $academic_calendar_id"
            wire:loading.attr="disabled"
            wire:target="generateWeeklyCoverage"
            loading="Generating…">
            <i class="bx bx-calendar-plus"></i> Generate Weeks
        </x-wizard.btn>
    </x-wizard.empty>
--}}

@props([
    'icon',
    'title',
    'message' => null,
    'dashed'  => true,
])

<div {{ $attributes->class([
    'rounded-xl py-14 text-center',
    'border-2 border-dashed border-slate-200 bg-slate-50/50' => $dashed,
    'border border-slate-200 bg-slate-50'                    => ! $dashed,
]) }}>

    <div class="flex flex-col items-center gap-4">

        {{-- Icon in a raised bubble --}}
        <div class="w-14 h-14 rounded-2xl bg-white border border-slate-200 shadow-sm
                    flex items-center justify-center" aria-hidden="true">
            <i class="bx bx-{{ $icon }} text-3xl text-slate-300"></i>
        </div>

        {{-- Text --}}
        <div class="max-w-xs px-4">
            <p class="text-sm font-semibold text-slate-600">{{ $title }}</p>
            @if ($message)
                <p class="mt-1 text-xs text-slate-400 leading-relaxed">{{ $message }}</p>
            @endif
        </div>

        {{-- CTA slot --}}
        @if ($slot->isNotEmpty())
            <div class="mt-1 flex flex-wrap items-center justify-center gap-2">
                {{ $slot }}
            </div>
        @endif

    </div>
</div>
