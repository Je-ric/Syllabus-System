@props(['action' => ''])

@php
    // Pre-defined action badge configurations for instant lookup
    $badgeConfig = [
        'created'    => ['bg-[#f0fdf4] text-[#166534] ring-[#bbf7d0]',   'bx-plus-circle'],
        'updated'    => ['bg-[#eff6ff] text-[#1e40af] ring-[#bfdbfe]',   'bx-edit'],
        'deleted'    => ['bg-[#fff1f2] text-[#9f1239] ring-[#fda4af]',   'bx-trash'],
        'login'      => ['bg-[#faf5ff] text-[#581c87] ring-[#d8b4fe]',   'bx-log-in'],
        'logout'     => ['bg-[#f8fafc] text-[#475569] ring-[#e2e8f0]',   'bx-log-out'],
        'approved'           => ['bg-[#f0fdf4] text-[#166534] ring-[#bbf7d0]',   'bx-check-circle'],
        'denied'             => ['bg-[#fff1f2] text-[#9f1239] ring-[#fda4af]',   'bx-x-circle'],
        'rejected'           => ['bg-[#fff1f2] text-[#9f1239] ring-[#fda4af]',   'bx-x-circle'],
        'recommended_approval' => ['bg-[#f0fdf4] text-[#166534] ring-[#bbf7d0]',   'bx-like'],
        'submitted'               => ['bg-[#eff6ff] text-[#1e40af] ring-[#bfdbfe]',   'bx-send'],
        'resubmitted_for_review' => ['bg-[#eff6ff] text-[#1e40af] ring-[#bfdbfe]',   'bx-refresh'],
        'saved_version'          => ['bg-[#fef3c7] text-[#92400e] ring-[#fde68a]',   'bx-save'],
        'decision_recorded'      => ['bg-[#f0fdf4] text-[#166534] ring-[#bbf7d0]',   'bx-check-double'],
        'verified_part_h'        => ['bg-[#f0fdf4] text-[#166534] ring-[#bbf7d0]',   'bx-check-shield'],
        'reviewer_assigned'      => ['bg-[#eff6ff] text-[#1e40af] ring-[#bfdbfe]',   'bx-user-plus'],
        'reviewer_removed'       => ['bg-[#fff1f2] text-[#9f1239] ring-[#fda4af]',   'bx-user-minus'],
        'reviewer_status_updated'=> ['bg-[#fef3c7] text-[#92400e] ring-[#fde68a]',   'bx-user-check'],
        'approved_by_set'        => ['bg-[#f0fdf4] text-[#166534] ring-[#bbf7d0]',   'bx-user-check'],
        'approved_by_cleared'    => ['bg-[#f8fafc] text-[#475569] ring-[#e2e8f0]',   'bx-user-x'],
        'concurred_by_set'       => ['bg-[#f0fdf4] text-[#166534] ring-[#bbf7d0]',   'bx-check-double'],
        'concurred_by_cleared'   => ['bg-[#f8fafc] text-[#475569] ring-[#e2e8f0]',   'bx-x-circle'],
        'saved'           => ['bg-[#fef3c7] text-[#92400e] ring-[#fde68a]',   'bx-save'],
        'mapped'          => ['bg-[#eff6ff] text-[#1e40af] ring-[#bfdbfe]',   'bx-link'],
        'unmapped'        => ['bg-[#f8fafc] text-[#475569] ring-[#e2e8f0]',   'bx-unlink'],
        'set_active'      => ['bg-[#f0fdf4] text-[#166534] ring-[#bbf7d0]',   'bx-bolt'],
        'disabled'        => ['bg-[#fff1f2] text-[#9f1239] ring-[#fda4af]',   'bx-block'],
        'restored'        => ['bg-[#f0fdf4] text-[#166534] ring-[#bbf7d0]',   'bx-refresh'],
        'assigned_roles'  => ['bg-[#eff6ff] text-[#1e40af] ring-[#bfdbfe]',   'bx-user-voice'],
    ];
    
    $badge = $badgeConfig[$action] ?? ['bg-[#f8fafc] text-[#475569] ring-[#e2e8f0]',   'bx-circle'];
@endphp

<span class="inline-flex items-center gap-1 rounded-lg px-2 py-0.5 text-[13px] font-medium ring-1 whitespace-nowrap {{ $badge[0] }}">
    <i class="bx {{ $badge[1] }} text-[11px] leading-none"></i>
    {{ ucfirst(str_replace('_', ' ', $action)) }}
</span>