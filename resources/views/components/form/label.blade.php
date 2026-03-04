@props([
    'for' => null,
    'class' => '',
    'icon' => null,
    'isRequired' => false,
    'variant' => null, // 'title', 'description', 'date', 'time', 'location', 'user', 'amount', 'notes', 'image'
])

@php
    $variants = [
        'title' => ['icon' => 'bx-book'],
        'description' => ['icon' => 'bx-align-left'],
        'date' => ['icon' => 'bx-calendar'],
        'year' => ['icon' => 'bx-time-five'],
    ];

    if ($variant && isset($variants[$variant])) {
        $icon = $variants[$variant]['icon'];
    }
@endphp

<label
    @if($for) for="{{ $for }}" @endif
    {{-- class="flex items-center text-sm font-medium sm:gap-2 text-[#1a2235] mb-2 {{ $class }}" --}}
    {{-- class="text-xs uppercase tracking-[0.25em] text-slate-500 {{ $class }}" --}}
    class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.15em] text-slate-500 pb-1.5"
>
    @if($icon)
        <i class="bx {{ $icon }} text-emerald-700 mr-1"></i>
    @endif
    {{ $slot }}
    @if($isRequired)
        <span class="text-rose-600 font-bold text-sm">*</span>
    @endif
</label>

{{--
Usage: <x-form.label for="email">Email Address</x-form.label>
       <x-form.label for="phone" class="text-lg">
           <i class="bx bx-phone"></i> Phone Number
       </x-form.label>
       <x-form.label for="title" icon="bx-book" icon-color="text-blue-600">Program Title</x-form.label>
       <x-form.label for="description" icon="bx-align-left" icon-color="text-green-600">Description</x-form.label>

       // Using predefined variants:
       <x-form.label for="title" variant="title">Program Title</x-form.label>
       <x-form.label for="description" variant="description">Description</x-form.label>
       <x-form.label for="date" variant="date">Date</x-form.label>
       <x-form.label for="user" variant="user">User Name</x-form.label>

--}}
