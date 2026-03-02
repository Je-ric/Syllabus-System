{{-- Notification stack --}}
{{-- If both are visible → they stack neatly
    If one disappears → layout auto-adjusts --}}
<div class="fixed top-30 inset-x-0 z-50 flex justify-center pointer-events-none">
    <div class="flex flex-col gap-2 items-center">

        {{-- Loading indicator --}}
        <div x-show="isSaving"
            class="px-4 py-2 rounded-full border border-amber-200 bg-amber-50 text-amber-900 text-xs font-semibold tracking-wide shadow-sm animate-pulse">
            <i class='bx bx-loader-alt bx-spin mr-2'></i> Saving PEOs...
        </div>

        {{-- Flash message --}}
        <template x-if="flashMessage">
            <div class="px-4 py-2 rounded-full border border-emerald-200 bg-emerald-50 text-emerald-900 text-xs font-semibold tracking-wide shadow-sm"
                x-text="flashMessage">
            </div>
        </template>

    </div>
</div>
