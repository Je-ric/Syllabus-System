@props([
    'rows'        => 3,
    'placeholder' => '',
])

{{--
    ROOT CAUSE FIX: {{ $slot }} must appear between <textarea> tags.
    HTML textarea has no `value` attribute — content must be inner text.

    Callers set existing value via slot content:
      <x-form.textarea name="goal_text">{{ $goal->goal_text }}</x-form.textarea>

    For old() fallback in create forms:
      <x-form.textarea name="goal_text">{{ old('goal_text') }}</x-form.textarea>

    For Alpine x-model — leave slot empty, Alpine manages inner content:
      <x-form.textarea x-model="peo.peo_text" />
--}}
<textarea
    rows="{{ $rows }}"
    placeholder="{{ $placeholder }}"
    {{ $attributes->merge([
        'class' => '
            w-full rounded-lg border border-slate-300 bg-white
            px-2.5 py-1.5 text-sm text-slate-700 shadow-sm
            placeholder:text-slate-400
            hover:border-slate-400
            focus:border-emerald-400 focus:ring-1 focus:ring-emerald-300 focus:outline-none
            disabled:bg-slate-50 disabled:text-slate-400 disabled:cursor-not-allowed disabled:border-slate-200
            resize-y transition-colors duration-150
        '
    ]) }}
>{{ $slot }}</textarea>
