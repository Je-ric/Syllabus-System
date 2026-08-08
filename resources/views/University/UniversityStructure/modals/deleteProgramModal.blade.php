<x-modal.dialog id="deleteProgramModal_{{ $program->id }}" maxWidth="max-w-md" width="w-11/12" variant="delete">
    <x-modal.header modalId="deleteProgramModal_{{ $program->id }}" variant="delete">
        <span class="text-[#9f1239]">Delete Program</span>
    </x-modal.header>

    <x-modal.body>
        <div class="space-y-4">
            <p class="text-[13px] text-[#475569]">Are you sure you want to delete this program?</p>

            <div class="rounded-xl border border-[#e2e8f0] bg-[#f8fafc] p-4 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">Program</span>
                    <span class="text-[13px] font-semibold text-[#0f172a] text-right max-w-[60%]">{{ $program->name }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">BOR Approval Resolution No.</span>
                    <span class="text-[13px] text-[#475569]">{{ $program->bor_approval_no }}</span>
                </div>
                @php $courseCount = $program->courses->count(); @endphp
                @if ($courseCount > 0)
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">Courses</span>
                        <span class="text-[13px] font-semibold text-rose-600">{{ $courseCount }}</span>
                    </div>
                @endif
            </div>

            <x-feedback-status.alert type="error" :showTitle="false">
                This will permanently delete the program and all its courses and syllabi.
            </x-feedback-status.alert>
        </div>
    </x-modal.body>

    <x-modal.footer>
        <x-modal.close-button :modalId="'deleteProgramModal_' . $program->id" text="Cancel" />
        <form action="{{ route('university.structure.program.destroy', $program->id) }}" method="POST"
            x-data="{ submitting: false }"
            x-on:submit="submitting = true">
            @csrf
            @method('DELETE')
            <x-ui.button type="submit" variant="danger"
                submitting="submitting" loadingText="Deleting…"
                ::disabled="submitting">
                <i class="bx bx-trash"></i> Delete Program
            </x-ui.button>
        </form>
    </x-modal.footer>
</x-modal.dialog>
