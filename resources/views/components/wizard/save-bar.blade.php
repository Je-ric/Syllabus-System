{{--
    x-wizard.save-bar
    ────────────────────────────────────────────────────────────────────────
    Sticky bottom save bar used in wizard steps that have a "Save All"
    action (Course Components, Course Evaluation).

    Props:
        hint        — hint text shown on the left (sm+)
        savingVar   — Alpine variable name that controls the spinner state
                      (default: '_saving')
        wireTarget  — wire:target value for the save button (optional)

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
            rounded-xl border border-[#dedee2] bg-white/95 backdrop-blur-sm"
     style="box-shadow: 0 -2px 16px rgba(0,0,0,.10);">

    @if ($hint)
        <p class="text-xs text-slate-400 hidden sm:block">{{ $hint }}</p>
    @else
        <div></div>
    @endif

    {{ $action ?? $slot }}

</div>
