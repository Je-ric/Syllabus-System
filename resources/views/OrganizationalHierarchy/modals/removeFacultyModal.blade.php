@props([
    'departmentId',
    'departmentName',
    'userId',
    'userName',
])

<x-modal.dialog id="removeFacultyModal-{{ $departmentId }}" maxWidth="max-w-md" width="w-11/12" variant="delete">
    <x-modal.header :modalId="'removeFacultyModal-' . $departmentId" variant="delete">
        <span class="text-[#9f1239]">Remove Faculty</span>
    </x-modal.header>

    <x-modal.body>
        <div class="space-y-4">
            <p class="text-[13px] text-[#475569]">Are you sure you want to remove this faculty member?</p>

            <div class="rounded-xl border border-[#e2e8f0] bg-[#f8fafc] p-4 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">Faculty</span>
                    <span class="text-[13px] font-semibold text-[#0f172a]">{{ $userName }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">Department</span>
                    <span class="text-[13px] font-semibold text-[#0f172a]">{{ $departmentName }}</span>
                </div>
            </div>

            <x-feedback-status.alert type="warning" :showTitle="false">
                This will remove the faculty member from this department.
            </x-feedback-status.alert>
        </div>
    </x-modal.body>

    <x-modal.footer>
        <x-modal.close-button :modalId="'removeFacultyModal-' . $departmentId" text="Cancel" />
        <form action="{{ route('organizational.remove-faculty') }}" method="POST">
            @csrf
            <input type="hidden" name="department_id" value="{{ $departmentId }}">
            <input type="hidden" name="user_id" value="{{ $userId }}">
            <x-button type="submit" variant="danger">
                <i class="bx bx-trash"></i> Remove Faculty
            </x-button>
        </form>
    </x-modal.footer>
</x-modal.dialog>
