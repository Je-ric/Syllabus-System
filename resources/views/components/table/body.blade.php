@props(['class' => ''])

<tbody {{ $attributes->merge(['class' => "divide-y divide-[#E3E8EB] $class"]) }}>
    {{ $slot }}
</tbody>
