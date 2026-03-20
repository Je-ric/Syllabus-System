@props([
    'rows'        => 3,
    'placeholder' => '',
])

{{--
    x-form.textarea
    ─────────────────────────────────────────────────────────────────────
    Unified with x-form.input / x-form.select.
    Padding: px-3 py-2 — consistent with all form controls.

    Value via slot (for Blade/old()):
      <x-form.textarea name="text">{{ old('text', $model->text) }}</x-form.textarea>

    Value via Alpine x-model (leave slot empty):
      <x-form.textarea x-model="draft.text" rows="4" />

    Value via Livewire wire:model:
      <x-form.textarea wire:model.defer="field" rows="3" />
--}}
<textarea
    rows="{{ $rows }}"
    placeholder="{{ $placeholder }}"
    {{ $attributes->merge([
        'class' => '
            w-full rounded-lg border border-slate-300 bg-white
            px-3 py-2 text-sm text-slate-700 shadow-sm
            placeholder:text-slate-400
            hover:border-slate-400
            focus:border-emerald-400 focus:ring-1 focus:ring-emerald-300 focus:outline-none
            disabled:bg-slate-50 disabled:text-slate-400 disabled:cursor-not-allowed disabled:border-slate-200
            resize-y transition-colors duration-150
        '
    ]) }}
>{{ $slot }}</textarea>
