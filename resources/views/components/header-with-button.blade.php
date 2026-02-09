@props([
    'title',
    'description' => null,
])

<div class="w-full mx-auto flex flex-col md:flex-row justify-between items-start md:items-center gap-6 md:gap-0 mb-5
            border-b-4 border-yellow-500 pb-4">
    <div class="flex flex-col grow">
        <h2 class="text-green-900 text-2xl md:text-3xl font-bold tracking-tight mb-1">
            {{ $title }}
        </h2>

        @if ($description)
            <p class="text-green-800 text-sm md:text-base font-light tracking-tight leading-relaxed">
                {{ $description }}
            </p>
        @endif

    </div>
    <div class="mt-4 md:mt-0">
        {{ $slot }} {{-- Anything, HAHAHAHA atleast hindi nagcecenter  --}}
    </div>
</div>


{{--
Usage: <x-header-with-button title="User Management" description="Manage user accounts and permissions">
            <x-button href="/users/create" variant="primary">Add User</x-button>
        </x-header-with-button>
        <x-header-with-button title="Dashboard">
            <x-button href="/settings" variant="secondary">Settings</x-button>
        </x-header-with-button>

--}}
