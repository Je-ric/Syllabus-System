<x-modal.dialog id="deleteCollegeModal_{{ $college->id }}" maxWidth="max-w-md" width="w-11/12" variant="delete">
    <x-modal.header modalId="deleteCollegeModal_{{ $college->id }}" variant="delete">
        <span class="text-[#9f1239]">Delete College</span>
    </x-modal.header>

    <x-modal.body>
        <div class="space-y-4">
            <p class="text-[13px] text-[#475569]">Are you sure you want to delete this college?</p>

            <div class="rounded-xl border border-[#e2e8f0] bg-[#f8fafc] p-4 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">College</span>
                    <span class="text-[13px] font-semibold text-[#0f172a]">{{ $college->name }}</span>
                </div>
                @php $deptCount = $college->departments->count(); @endphp
                @if ($deptCount > 0)
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">Departments</span>
                        <span class="text-[13px] font-semibold text-rose-600">{{ $deptCount }}</span>
                    </div>
                @endif
            </div>

            <x-feedback-status.alert type="error" :showTitle="false">
                This will permanently delete the college and all its departments, programs, courses, and syllabi.
            </x-feedback-status.alert>
        </div>
    </x-modal.body>

    <x-modal.footer>
        <x-modal.close-button :modalId="'deleteCollegeModal_' . $college->id" text="Cancel" />
        <form action="{{ route('college.destroy', $college->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <x-button type="submit" variant="danger">
                <i class="bx bx-trash"></i> Delete College
            </x-button>
        </form>
    </x-modal.footer>
</x-modal.dialog>
