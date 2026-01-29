@props([
    'for' => null,
    'class' => '',
    'icon' => null,
    'iconColor' => 'text-gray-600',
    'isRequired' => false,
    'variant' => null, // 'title', 'description', 'date', 'time', 'location', 'user', 'amount', 'notes', 'image'
])

@php
    $variants = [
        'title' => ['icon' => 'bx-book', 'color' => 'text-blue-600'],
        'description' => ['icon' => 'bx-align-left', 'color' => 'text-green-600'],
        'date' => ['icon' => 'bx-calendar', 'color' => 'text-green-500'],
        'year' => ['icon' => 'bx-time-five', 'color' => 'text-green-600'],
    ];

    if ($variant && isset($variants[$variant])) {
        $icon = $variants[$variant]['icon'];
        $iconColor = $variants[$variant]['color'];
    }
@endphp

<label
    @if($for) for="{{ $for }}" @endif
    class="flex items-center text-sm font-medium sm:gap-2 text-[#1a2235] mb-2 {{ $class }}"
>
    @if($icon)
        <i class="bx {{ $icon }} {{ $iconColor }} mr-1"></i>
    @endif
    {{ $slot }}
    @if($isRequired)
        <span class="text-red-600">*</span>
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
