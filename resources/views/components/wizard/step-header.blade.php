{{--
    x-wizard.step-header
    ─────────────────────────────────────────────────────────────────────────────
    Consistent page-level heading for every wizard step.

    UX principles applied:
      Visual Hierarchy  — Bold title draws the eye first, muted description second,
                          actions third (right-aligned, separated by the left content)
      Affordance        — Icon gives instant context of what the step is about
      Consistency       — Same emerald + slate palette across all steps
      Accessibility     — <h3> for screen readers; role="region" for landmark nav

    Props:
      title        string  required  — step name
      description  string  optional  — one-sentence guide shown below the title
      icon         string  optional  — boxicons name WITHOUT "bx-" prefix
                                       e.g. icon="calendar" → <i class="bx bx-calendar">

    Default slot (optional):
      Primary action button(s) that belong to this step.
      Aligned right, vertically centred with the title block.

    ─── USAGE ────────────────────────────────────────────────────────────────────
    Basic:
      <x-wizard.step-header
          title="Academic Calendar"
          icon="calendar"
          description="Choose the academic year and semester for this syllabus." />

    With right-side action:
      <x-wizard.step-header
          title="Weekly Coverage"
          icon="calendar-week"
          description="Fill in coverage details per week.">

          <x-wizard.btn variant="sm-warning"
              wire:click="regenerateWeeks"
              wire:target="regenerateWeeks"
              loading="Regenerating…">
              <i class="bx bx-refresh"></i> Regenerate
          </x-wizard.btn>
          <x-wizard.btn variant="sm-success"
              wire:click="saveAllWeeklyEntries"
              wire:target="saveAllWeeklyEntries"
              loading="Saving…">
              <i class="bx bx-save"></i> Save All
          </x-wizard.btn>
      </x-wizard.step-header>
--}}

@props([
    'title',
    'description' => null,
    'icon'        => null,
])

<div class="mb-6 pb-5 border-b border-slate-100" role="region" aria-label="{{ $title }}">
    <div class="flex items-start justify-between gap-4">

        {{-- Left: icon + text block ─────────────────────────────────────────── --}}
        <div class="flex items-start gap-3 min-w-0">

            {{-- Icon bubble — gives instant visual context for the step --}}
            @if ($icon)
                <div aria-hidden="true"
                    class="shrink-0 mt-0.5 flex items-center justify-center
                           w-10 h-10 rounded-xl
                           bg-gradient-to-br from-emerald-500 to-[#009639]
                           text-white shadow-sm">
                    <i class="bx bx-{{ $icon }} text-xl leading-none"></i>
                </div>
            @endif

            <div class="min-w-0">

                {{-- Primary heading — largest element, read first --}}
                <h3 class="text-xl font-bold tracking-tight text-slate-900 leading-snug">
                    {{ $title }}
                </h3>

                {{-- Secondary description — smaller, muted, guides intent --}}
                @if ($description)
                    <p class="mt-1 text-sm text-slate-500 leading-relaxed max-w-2xl">
                        {{ $description }}
                    </p>
                @endif

            </div>
        </div>

        {{-- Right: action slot — tertiary; only visible if caller provides content --}}
        @if ($slot->isNotEmpty())
            <div class="shrink-0 flex items-center flex-wrap gap-2 pt-0.5">
                {{ $slot }}
            </div>
        @endif

    </div>
</div>
