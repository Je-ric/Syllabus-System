@props(['class' => ''])

<div {{ $attributes->merge(['class' => "overflow-x-auto rounded-[12px] border border-[#E3E8EB] bg-white $class"]) }}
     style="box-shadow: 0 1px 2px rgba(16,24,40,0.04), 0 1px 3px rgba(16,24,40,0.06);">
    {{ $slot }}
</div>
