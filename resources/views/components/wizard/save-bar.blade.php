{{--
    x-wizard.save-bar
    ────────────────────────────────────────────────────────────────────────
    Sticky bottom save bar used in wizard steps that have a "Save All"
    action (Course Components, Course Evaluation).

    Props:
        hint        — hint text shown on the left (sm+)

    The save button action is passed via the `action` slot so the parent
    can wire up whatever click handler it needs.

    Usage (Course Components):
        <x-wizard.save-bar hint="Changes in both LEC and LAB sections are saved together.">
            <x-slot:action>
                <button type="button" x-bind:disabled="_saving" x-on:click="...">
                    ...
                </button>
            </x-slot:action>
        </x-wizard.save-bar>
──────────────────────────────────────────────────────────────────────────--}}
@props([
    'hint' => null,
])

<div class="sticky bottom-0 z-10 mt-4 flex items-center justify-between gap-4 px-5 py-3
            rounded-[10px] border border-[#E3E8EB] bg-white/95 backdrop-blur-sm"
     style="box-shadow: 0 -2px 12px rgba(16,24,40,0.08), 0 -1px 3px rgba(16,24,40,0.04);">

    @if ($hint)
        <p class="text-[12px] text-[#93A1AF] hidden sm:block leading-snug">{{ $hint }}</p>
    @else
        <div></div>
    @endif

    {{ $action ?? $slot }}

</div>
