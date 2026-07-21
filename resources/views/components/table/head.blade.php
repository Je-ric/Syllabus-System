@props(['sticky' => false, 'class' => ''])

@php $stickyClass = $sticky ? 'sticky top-0 z-10' : ''; @endphp

<thead {{ $attributes->merge(['class' => "bg-[#F1F3F5] border-b border-[#E3E8EB] $stickyClass $class"]) }}>
    {{ $slot }}
</thead>
