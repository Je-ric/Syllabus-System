<div class="bg-white border rounded p-4">
    <h2 class="font-semibold mb-3">Program Outcomes (POs)</h2>

    {{-- Show PEOs of the selected program for reference --}}
    <livewire:programs.peo-display :program="$program" />

    {{-- Manage POs and map to PEOs --}}
    <livewire:programs.manage-pos :program="$program" />
</div>
