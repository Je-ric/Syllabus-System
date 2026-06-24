{{-- components-partials/consultation-hours-modal.blade.php
     Required x-data in parent: { hours, days, saving, addRow(), removeRow(i), save() }
     Trigger: document.getElementById('consultation-hours-modal').showModal()
--}}
<x-modal.dialog id="consultation-hours-modal" maxWidth="max-w-md">
    <x-modal.header modalId="consultation-hours-modal" variant="edit">
        Consultation Hours
    </x-modal.header>

    <x-modal.body class="space-y-2.5">
        <template x-for="(row, i) in hours" :key="i">
            <div class="flex items-center gap-2">
                <select x-model="row.day"
                    class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-[13px] focus:border-emerald-400 focus:outline-none">
                    <template x-for="d in days" :key="d">
                        <option :value="d" x-text="d"></option>
                    </template>
                </select>
                <input type="text" x-model="row.time" placeholder="e.g. 09:00 AM – 11:00 AM"
                    class="flex-1 text-[13px] rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 focus:border-emerald-400 focus:outline-none focus:bg-white placeholder:text-slate-300" />
                <button type="button" x-on:click="removeRow(i)"
                    class="p-1.5 text-slate-300 hover:text-rose-500 hover:bg-rose-50 rounded-md transition-colors">
                    <i class="bx bx-trash text-sm"></i>
                </button>
            </div>
        </template>
        <button type="button" x-on:click="addRow()"
            class="w-full flex items-center justify-center gap-1.5 py-2 rounded-lg border-2 border-dashed border-slate-200 text-[12px] font-semibold text-slate-400 hover:border-emerald-300 hover:text-emerald-600 transition-colors">
            <i class="bx bx-plus text-sm"></i> Add Row
        </button>
    </x-modal.body>

    <x-modal.footer>
        <x-modal.close-button modalId="consultation-hours-modal" text="Cancel" x-bind:disabled="saving" />
        <x-button variant="add-button" type="button" x-on:click="save()" x-bind:disabled="saving">
            <i x-show="!saving" class="bx bx-save text-sm leading-none"></i>
            <svg x-show="saving" x-cloak class="animate-spin h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <span x-text="saving ? 'Saving…' : 'Save'"></span>
        </x-button>
    </x-modal.footer>
</x-modal.dialog>
