{{--
    x-form.time-range
    ────────────────────────────────────────────────────────────────────────
    A single day-selector + start/end time input row with a remove button.
    Designed for use inside Alpine x-for loops.

    Props:
        color   — 'emerald' | 'blue' | 'amber'  (focus ring color)
        label   — aria-label prefix for the group (e.g. 'LEC schedule row')
        index   — row index (for aria-label)

    The parent x-for loop owns the data model; this component only renders
    the inputs. Bind x-model on the parent loop variable.

    Usage:
        <template x-for="(row, i) in schedules" :key="i">
            <x-form.time-range color="emerald" :index="i + 1" label="LEC schedule row"
                x-model-day="row.day"
                x-model-start="row.startTime"
                x-model-end="row.endTime"
                x-on:remove="schedules.splice(i, 1)" />
        </template>

    Because Blade components cannot forward Alpine x-model directives
    directly, the bindings are passed as plain attributes and rendered
    verbatim via $attributes.
──────────────────────────────────────────────────────────────────────────--}}
@props([
    'color' => 'emerald',
    'label' => 'row',
    'index' => 1,
])

@php
    $focusBorder = match($color) {
        'blue'  => 'focus:border-blue-400',
        'amber' => 'focus:border-amber-400',
        default => 'focus:border-emerald-400',
    };
    $inputBase = "flex-1 text-sm rounded-lg border border-[#e2e8f0] bg-[#f8fafc] px-3 py-2
                  focus:outline-none focus:bg-white transition-colors $focusBorder";
@endphp

<div class="flex items-center gap-2" role="group" :aria-label="'{{ $label }} ' + {{ $index }}">

    {{-- Day selector --}}
    <x-form.select {{ $attributes->only(['x-model.day', ':x-model']) }} aria-label="Day"
        x-bind:value="{{ $attributes->get('x-model-day') }}"
        x-on:change="{{ $attributes->get('x-model-day') }} = $event.target.value">
        @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $d)
            <option value="{{ $d }}">{{ $d }}</option>
        @endforeach
    </x-form.select>

    <input type="time"
        x-model="{{ $attributes->get('x-model-start') }}"
        aria-label="Start time"
        class="{{ $inputBase }}" />

    <span class="text-xs text-slate-400 shrink-0">to</span>

    <input type="time"
        x-model="{{ $attributes->get('x-model-end') }}"
        aria-label="End time"
        class="{{ $inputBase }}" />

    <button type="button"
        x-on:click="{{ $attributes->get('x-on:remove', '') }}"
        class="p-1.5 text-[#94a3b8] hover:text-rose-500 hover:bg-rose-50 rounded-md transition"
        aria-label="Remove row">
        <i class="bx bx-trash text-sm"></i>
    </button>

</div>
