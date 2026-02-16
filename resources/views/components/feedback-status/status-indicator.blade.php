@props(['status',
        'label' => null,
        'icon' => null
        ])

@php
$variant = [
    // General statuses
    'success' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200',
    'neutral' => 'bg-slate-50 text-slate-600 ring-1 ring-slate-200',
    'info'    => 'bg-blue-50 text-blue-700 ring-1 ring-blue-200',
    'warning' => 'bg-amber-50 text-amber-700 ring-1 ring-amber-200',
    'danger'  => 'bg-rose-50 text-rose-700 ring-1 ring-rose-200',

    // User Account Statuses
    'active'   => 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-300',
    'pending'  => 'bg-amber-50 text-amber-800 ring-1 ring-amber-300',
    'rejected' => 'bg-rose-50 text-rose-800 ring-1 ring-rose-300',
    'disabled' => 'bg-slate-100 text-slate-700 ring-1 ring-slate-300',

    // User Roles
    'admin'    => 'bg-purple-50 text-purple-800 ring-1 ring-purple-300',
    'dean'     => 'bg-indigo-50 text-indigo-800 ring-1 ring-indigo-300',
    'chair'    => 'bg-blue-50 text-blue-800 ring-1 ring-blue-300',
    'faculty'  => 'bg-green-50 text-green-800 ring-1 ring-green-300',

    // Course Components
    'lec' => 'bg-sky-50 text-sky-700 ring-1 ring-sky-200',
    'lec_lab' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200',
];

$icons = [
    'success' => 'bx bx-check-circle',
    'neutral' => 'bx bx-minus-circle',
    'info' => 'bx bx-info-circle',
    'warning' => 'bx bx-error-circle',
    'danger' => 'bx bx-x-circle',
    'active' => 'bx bx-check-shield',
    'pending' => 'bx bx-time-five',
    'rejected' => 'bx bx-block',
    'disabled' => 'bx bx-pause-circle',
    'admin' => 'bx bx-crown',
    'dean' => 'bx bx-medal',
    'chair' => 'bx bx-user-pin',
    'faculty' => 'bx bx-user',
    'lec' => 'bx bx-book',
    'lec_lab' => 'bx bx-flask',
];

$style = $variant[$status] ?? 'bg-slate-100 text-slate-700 ring-1 ring-slate-300';
$iconClass = $icon ?? ($icons[$status] ?? '');
@endphp

<span class="inline-flex items-center gap-1 px-3 py-1 text-xs rounded-full font-semibold {{ $style }}">
    @if($iconClass)
        <i class="{{ $iconClass }}"></i>
    @endif
    {{ $label ?? ucfirst(str_replace('_', ' ', $status)) }}
</span>

{{--
Usage: <x-feedback-status.status-indicator status="success" />
        <x-feedback-status.status-indicator status="pending" label="Awaiting Approval" />
        <x-feedback-status.status-indicator status="published" icon="bx-check-circle" />
--}}
