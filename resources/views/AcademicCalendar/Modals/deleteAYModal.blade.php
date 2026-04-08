<x-modal.dialog id="deleteAYModal_{{ str_replace('-', '_', $year) }}" maxWidth="max-w-md" width="w-11/12">
    <x-modal.header modalId="deleteAYModal_{{ str_replace('-', '_', $year) }}">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-[#ffe4e6] text-[#e11d48] shrink-0">
                <i class="bx bx-trash text-base leading-none"></i>
            </span>
            <span class="text-[#9f1239]">Delete Academic Year</span>
        </div>
    </x-modal.header>

    <x-modal.body>
        <div class="space-y-4">
            <p class="text-[13px] text-[#475569]">Are you sure you want to delete this academic year? All semesters will also be permanently removed.</p>

            <div class="rounded-xl border border-[#e2e8f0] bg-[#f8fafc] p-4 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">Academic Year</span>
                    <span class="text-[13px] font-bold text-[#0f172a]">{{ $year }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">Semesters</span>
                    <span class="text-[13px] text-[#475569]">{{ $semesters->count() }}</span>
                </div>
                @foreach ($semesters as $sem)
                    <div class="flex items-center justify-between pl-3">
                        <span class="text-[13px] text-[#94a3b8]">{{ $sem->semester }} Semester</span>
                        <span class="text-[13px] text-[#475569]">
                            {{ \Carbon\Carbon::parse($sem->start_date)->format('M j') }}
                            – {{ \Carbon\Carbon::parse($sem->end_date)->format('M j, Y') }}
                        </span>
                    </div>
                @endforeach
            </div>

            <x-feedback-status.alert type="error" :showTitle="false">This action cannot be undone.</x-feedback-status.alert>
        </div>
    </x-modal.body>

    <x-modal.footer>
        <x-modal.close-button :modalId="'deleteAYModal_' . str_replace('-', '_', $year)" text="Cancel" />
        <form action="{{ route('academic.calendars.destroy', $year) }}" method="POST">
            @csrf
            @method('DELETE')
            <x-button type="submit" variant="danger">
                <i class="bx bx-trash"></i> Delete A.Y.
            </x-button>
        </form>
    </x-modal.footer>
</x-modal.dialog>
