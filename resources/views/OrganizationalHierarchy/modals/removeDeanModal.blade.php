@props([
    'collegeId',
    'collegeName',
    'userId',
    'userName',
])

<x-modal.dialog id="removeDeanModal-{{ $collegeId }}" maxWidth="max-w-md" width="w-11/12" variant="delete">
    <x-modal.header :modalId="'removeDeanModal-' . $collegeId" variant="delete">
        <span class="text-[#9f1239]">Remove Dean</span>
    </x-modal.header>

    <x-modal.body>
        <div class="space-y-4">
            <p class="text-[13px] text-[#475569]">Are you sure you want to remove this dean assignment?</p>

            <div class="rounded-xl border border-[#e2e8f0] bg-[#f8fafc] p-4 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">Dean</span>
                    <span class="text-[13px] font-semibold text-[#0f172a]">{{ $userName }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">College</span>
                    <span class="text-[13px] font-semibold text-[#0f172a]">{{ $collegeName }}</span>
                </div>
            </div>

            <x-feedback-status.alert type="warning" :showTitle="false">
                This will only remove the dean assignment. The user will retain their faculty role.
            </x-feedback-status.alert>
        </div>
    </x-modal.body>

    <x-modal.footer>
        <x-modal.close-button :modalId="'removeDeanModal-' . $collegeId" text="Cancel" />
        <form action="{{ route('organizational.remove-dean') }}" method="POST">
            @csrf
            <input type="hidden" name="college_id" value="{{ $collegeId }}">
            <input type="hidden" name="user_id" value="{{ $userId }}">
            <x-button type="submit" variant="danger">
                <i class="bx bx-trash"></i> Remove Dean
            </x-button>
        </form>
    </x-modal.footer>
</x-modal.dialog>
