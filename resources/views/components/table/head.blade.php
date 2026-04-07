@props(['sticky' => false, 'class' => ''])

@php $stickyClass = $sticky ? 'sticky top-0 z-10' : ''; @endphp

<thead {{ $attributes->merge(['class' => "bg-[#f8fafc] border-b border-[#e2e8f0] $stickyClass $class"]) }}>
    {{ $slot }}
</thead>
