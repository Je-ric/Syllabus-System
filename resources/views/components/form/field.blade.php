@props([
    'label'      => null,
    'for'        => null,
    'error'      => null,       // Livewire @error key or raw message string
    'isRequired' => false,
    'icon'       => null,
    'hint'       => null,       // small helper text below the field
])

{{--
    x-form.field
    ─────────────────────────────────────────────────────────────────────
    Wraps label + any form control + validation error + optional hint.
    Eliminates the repetitive label/error boilerplate in every form.

    USAGE — with Livewire wire:model:
      <x-form.field label="Instructor Name" for="lec_name" :isRequired="true"
                    error="lec_instructor_name">
          <x-form.input wire:model.defer="lec_instructor_name"
                        placeholder="Enter name…" />
      </x-form.field>

    USAGE — with Alpine x-model:
      <x-form.field label="PEO Text">
          <x-form.textarea x-model="peo.peo_text" rows="3" />
      </x-form.field>

    USAGE — with hint:
      <x-form.field label="Passing Mark" hint="Applied to both LEC and LAB.">
          <x-form.select wire:model.defer="lec_performance_standard">…</x-form.select>
      </x-form.field>
--}}

<div {{ $attributes->class(['space-y-1']) }}>

    @if ($label)
        <x-form.label :for="$for" :isRequired="$isRequired" :icon="$icon">
            {{ $label }}
        </x-form.label>
    @endif

    {{ $slot }}

    @if ($error)
        @error($error)
            <p class="flex items-center gap-1 text-xs text-rose-600 mt-0.5">
                <i class="bx bx-error-circle text-sm leading-none shrink-0"></i>
                {{ $message }}
            </p>
        @enderror
    @endif

    @if ($hint)
        <p class="text-[11px] text-slate-400 leading-snug">{{ $hint }}</p>
    @endif

</div>
