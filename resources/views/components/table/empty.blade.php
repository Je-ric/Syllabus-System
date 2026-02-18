@props([
    'colspan' => 1,
    'message' => 'No records found.',
    'class' => '',
])

<x-table.row>
    <x-table.td :colspan="$colspan" align="center" class="text-slate-500 {{ $class }}">
        {{ $message }}
    </x-table.td>
</x-table.row>
