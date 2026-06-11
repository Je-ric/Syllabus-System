<x-card-section icon="bx-filter-alt" title="Filters">
    <x-slot:actions>
        <button wire:click="$set('search',''); $set('role','all'); $set('status','all'); $set('sort','newest')"
            class="text-[11px] text-[#94a3b8] hover:text-rose-500 transition flex items-center gap-1 whitespace-nowrap">
            <i class="bx bx-reset text-xs leading-none"></i> Clear
        </button>
    </x-slot:actions>

    <div class="flex flex-wrap gap-2 items-center">
        <div class="relative flex-1 min-w-45">
            <i class="bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-[#94a3b8] text-base pointer-events-none"></i>
            <x-form.input type="text" wire:model.live.debounce.300ms="search"
                placeholder="Search name, email, phone…" class="pl-9 py-2! text-[13px]" />
        </div>

        <x-form.select wire:model.live="role" class="py-2! text-[13px] min-w-30">
            <option value="all">All Roles</option>
            <option value="admin">Admin</option>
            <option value="dean">Dean</option>
            <option value="chair">Chair</option>
            <option value="faculty">Faculty</option>
        </x-form.select>

        <x-form.select wire:model.live="status" class="py-2! text-[13px] min-w-32.5">
            <option value="all">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="active">Active</option>
            <option value="disabled">Disabled</option>
            <option value="rejected">Rejected</option>
        </x-form.select>

        <x-form.select wire:model.live="sort" class="py-2! text-[13px] min-w-32.5">
            <option value="newest">Newest First</option>
            <option value="oldest">Oldest First</option>
            <option value="name_asc">Name A–Z</option>
            <option value="name_desc">Name Z–A</option>
            <option value="status_asc">Status A–Z</option>
            <option value="status_desc">Status Z–A</option>
        </x-form.select>
    </div>
</x-card-section>
