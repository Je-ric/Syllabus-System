<div class="bg-white border rounded p-4">
    <h2 class="font-semibold mb-3">Program Outcomes (POs)</h2>

    {{-- Show PEOs of the selected program for reference --}}
    <div class="mb-4">
        <h3 class="text-sm font-medium text-gray-700">Program Educational Objectives (PEOs)</h3>
        <div class="mt-1 flex flex-wrap gap-2">
            @foreach ($program->peos()
                            ->orderBy('peo_code')
                            ->get(['peo_code','peo_text']) as $peo)
                <span class="px-2 py-1 text-xs rounded bg-gray-50 border">{{ $peo->peo_code }}: {{ $peo->peo_text }}</span>
            @endforeach
        </div>
    </div>

    {{-- Manage POs and map to PEOs --}}
    <livewire:programs.manage-pos :program="$program" />
</div>
