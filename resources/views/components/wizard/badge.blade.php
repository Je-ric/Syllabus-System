@props([
    'variant' => 'slate',
    'icon'    => null,
    'dot'     => false,
])

@php
    $variants = [
        'emerald' => ['pill' => 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200', 'dot' => 'bg-emerald-500'],
        'blue'    => ['pill' => 'bg-blue-100 text-blue-700 ring-1 ring-blue-200',         'dot' => 'bg-blue-500'],
        'amber'   => ['pill' => 'bg-amber-100 text-amber-700 ring-1 ring-amber-200',      'dot' => 'bg-amber-500'],
        'rose'    => ['pill' => 'bg-rose-100 text-rose-700 ring-1 ring-rose-200',         'dot' => 'bg-rose-500'],
        'violet'  => ['pill' => 'bg-violet-100 text-violet-700 ring-1 ring-violet-200',   'dot' => 'bg-violet-500'],
        'sky'     => ['pill' => 'bg-sky-100 text-sky-700 ring-1 ring-sky-200',            'dot' => 'bg-sky-500'],
        'indigo'  => ['pill' => 'bg-indigo-100 text-indigo-700 ring-1 ring-indigo-200',   'dot' => 'bg-indigo-500'],
        'slate'   => ['pill' => 'bg-slate-100 text-slate-600 ring-1 ring-slate-200',      'dot' => 'bg-slate-400'],
    ];
    $v = $variants[$variant] ?? $variants['slate'];
@endphp

<span {{ $attributes->class([
    'inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-xs font-semibold',
    $v['pill'],
]) }}>
    @if ($dot)
        <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $v['dot'] }}" aria-hidden="true"></span>
    @elseif ($icon)
        <i class="bx bx-{{ $icon }} text-xs shrink-0" aria-hidden="true"></i>
    @endif
    {{ $slot }}
</span>
