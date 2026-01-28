<div class="bg-white border rounded p-4">
    <h2 class="font-semibold mb-3">Program Outcomes (POs)</h2>

    {{-- Show PEOs of the selected program for reference --}}
    <div class="mb-4">
        <h3 class="text-sm font-medium text-gray-700 mb-2">
            Program Educational Objectives (PEOs)
        </h3>

        <div class="space-y-1">
            @foreach (
                $program->peos()
                    ->orderBy('peo_code')
                    ->get(['peo_code', 'peo_text']) as $peo
            )
                <div class="text-sm text-gray-800 flex gap-2">
                    <span class="font-semibold whitespace-nowrap">
                        {{ $peo->peo_code }}:
                    </span>
                    <span>
                        {{ $peo->peo_text }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Manage POs and map to PEOs --}}
    <livewire:programs.manage-pos :program="$program" />
</div>
