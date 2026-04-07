@props(['colspan' => 1, 'message' => 'No records found.', 'class' => ''])

<x-table.row>
    <x-table.td :colspan="$colspan" align="center" class="py-10 text-[13px] text-[#94a3b8] italic {{ $class }}">
        {{ $message }}
    </x-table.td>
</x-table.row>
