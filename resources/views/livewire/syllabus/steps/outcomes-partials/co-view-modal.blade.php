{{-- outcomes-partials/co-view-modal.blade.php
     Required Alpine state in parent: viewModal = { open, co_code, description }
     Trigger: document.getElementById('co-view-modal').showModal()
--}}
<x-modal.dialog id="co-view-modal" maxWidth="max-w-2xl">
    <x-modal.header modalId="co-view-modal">
        <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-emerald-50 text-emerald-800 text-[11px] font-bold ring-2 ring-emerald-300 mr-1"
            x-text="viewModal.co_code"></span>
        Course Outcome
        <span class="text-[11px] font-normal text-slate-400 ml-1">— Read-only</span>
    </x-modal.header>

    <x-modal.body>
        <p class="text-[13px] text-slate-700 leading-relaxed" x-text="viewModal.description"></p>
    </x-modal.body>

    <x-modal.footer>
        <x-modal.close-button modalId="co-view-modal" />
    </x-modal.footer>
</x-modal.dialog>
