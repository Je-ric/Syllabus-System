<x-modal.dialog id="deleteDepartmentModal_{{ $dept->id }}" maxWidth="max-w-md" width="w-11/12" variant="delete">
    <x-modal.header modalId="deleteDepartmentModal_{{ $dept->id }}" variant="delete">
        <span class="text-[#9f1239]">Delete Department</span>
    </x-modal.header>

    <x-modal.body>
        <div class="space-y-4">
            <p class="text-[13px] text-[#475569]">Are you sure you want to delete this department?</p>

            <div class="rounded-xl border border-[#e2e8f0] bg-[#f8fafc] p-4 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">Department</span>
                    <span class="text-[13px] font-semibold text-[#0f172a]">{{ $dept->name }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">College</span>
                    <span class="text-[13px] text-[#475569]">{{ $dept->college->name }}</span>
                </div>
                @php $programCount = $dept->programs->count(); @endphp
                @if ($programCount > 0)
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">Programs</span>
                        <span class="text-[13px] font-semibold text-rose-600">{{ $programCount }}</span>
                    </div>
                @endif
            </div>

            <x-feedback-status.alert type="error" :showTitle="false">
                This will permanently delete the department and all its programs, courses, and syllabi.
            </x-feedback-status.alert>
        </div>
    </x-modal.body>

    <x-modal.footer>
        <x-modal.close-button :modalId="'deleteDepartmentModal_' . $dept->id" text="Cancel" />
        <form action="{{ route('university.structure.department.destroy', $dept->id) }}" method="POST"
            x-data="{ submitting: false }"
            x-on:submit="submitting = true">
            @csrf
            @method('DELETE')
            <x-ui.button type="submit" variant="danger"
                submitting="submitting" loadingText="Deleting…"
                ::disabled="submitting">
                <i class="bx bx-trash"></i> Delete Department
            </x-ui.button>
        </form>
    </x-modal.footer>
</x-modal.dialog>
