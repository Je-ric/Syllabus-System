@props(['colspan' => 1, 'message' => 'No records found.', 'class' => ''])

<x-table.row>
    <x-table.td :colspan="$colspan" align="center"
        class="py-12 {{ $class }}">
        <div class="flex flex-col items-center gap-2">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full
                         bg-[#F1F3F5] text-[#B4C0CA]">
                <i class="bx bx-data text-xl leading-none"></i>
            </span>
            <p class="text-[13px] text-[#93A1AF] font-medium">{{ $message }}</p>
        </div>
    </x-table.td>
</x-table.row>
