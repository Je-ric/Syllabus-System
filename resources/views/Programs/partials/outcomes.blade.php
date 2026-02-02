<div class="bg-white border rounded p-4">
    <h2 class="font-semibold mb-3">Program Outcomes (POs) and its Relationship to the Program Educational Objectives</h2>

    {{-- Show PEOs of the selected program for reference --}}
    <livewire:programs.peo-display :program="$program" />

    <p>By the time of graduation, students of the program have the ability to:</p>
    {{-- Manage POs and map to PEOs --}}
    <livewire:programs.manage-pos :program="$program" />
</div>
