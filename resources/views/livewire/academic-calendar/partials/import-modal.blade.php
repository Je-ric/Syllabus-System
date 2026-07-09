{{-- livewire/academic-calendar/partials/import-modal.blade.php --}}

<div
    x-show="showImport"
    x-cloak
    x-on:keydown.escape.window="showImport = false"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="background:rgba(0,0,0,0.45);">

    <div
        x-show="showImport"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        x-on:click.outside="showImport = false"
        class="w-full max-w-lg rounded-2xl bg-white shadow-2xl overflow-hidden">

        <div class="flex items-center justify-between px-5 py-4 border-b border-[#e2e8f0] bg-[#f8fafc]">
            <p class="text-[13px] font-bold text-[#0f172a] inline-flex items-center gap-1.5">
                <i class="bx bx-upload text-sm"></i> Import Events from CSV
            </p>
            <button type="button" x-on:click="showImport = false"
                class="w-7 h-7 flex items-center justify-center rounded-lg text-[#94a3b8]
                       hover:bg-[#f1f5f9] hover:text-[#0f172a] transition-colors">
                <i class="bx bx-x text-lg"></i>
            </button>
        </div>

        <div class="p-5">
            <div class="rounded-lg bg-[#f8fafc] border border-[#e2e8f0] p-4 mb-4 text-[12px] text-[#475569] space-y-1.5">
                <p class="font-semibold text-[#0f172a]">CSV Format</p>
                <p>Columns: <code class="bg-white border border-[#e2e8f0] rounded px-1">type, name, date</code></p>
                <p>Date format: <code class="bg-white border border-[#e2e8f0] rounded px-1">YYYY-MM-DD</code></p>
                <p>Types: <code class="bg-white border border-[#e2e8f0] rounded px-1">holiday</code>
                    <code class="bg-white border border-[#e2e8f0] rounded px-1">exam</code>
                    <code class="bg-white border border-[#e2e8f0] rounded px-1">break</code>
                    <code class="bg-white border border-[#e2e8f0] rounded px-1">non_teaching</code>
                    <code class="bg-white border border-[#e2e8f0] rounded px-1">other</code>
                </p>
            </div>

            <input type="file" wire:model="csvFile" accept=".csv,.txt"
                class="w-full text-[13px] text-[#475569]
                       file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                       file:text-[12px] file:font-semibold file:bg-[#dcfce7] file:text-[#166534]
                       hover:file:bg-[#bbf7d0] cursor-pointer" />

            @error('csvFile')
                <p class="mt-2 text-[13px] text-rose-500 flex items-center gap-1">
                    <i class="bx bx-error-circle"></i> {{ $message }}
                </p>
            @enderror
        </div>

        <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-[#e2e8f0] bg-[#f8fafc]">
            <button type="button" x-on:click="showImport = false"
                class="px-4 py-2 rounded-lg text-[13px] font-semibold text-[#475569]
                       border border-[#e2e8f0] bg-white hover:bg-[#f1f5f9] transition-colors">
                Cancel
            </button>
            <x-ui.button type="button" variant="add-button"
                wire:click="importCsv"
                wire:loading.attr="disabled"
                wire:target="importCsv,csvFile">
                <span wire:loading.remove wire:target="importCsv" class="inline-flex items-center gap-1.5">
                    <i class="bx bx-import text-sm"></i> Import
                </span>
                <span wire:loading wire:target="importCsv" class="inline-flex items-center gap-1.5">
                    <svg class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Importing…
                </span>
            </x-ui.button>
        </div>
    </div>
</div>
