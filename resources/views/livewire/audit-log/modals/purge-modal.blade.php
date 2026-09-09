@props([
    'confirmingPurge' => false,
    'purgeMonths' => 6,
    'protectSyllabusLogs' => true,
    'purgePreviewCount' => 0,
])

@php
    $monthsOptions = [
        3 => '3 months',
        6 => '6 months',
        12 => '1 year',
        24 => '2 years',
    ];
@endphp

@if ($confirmingPurge)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
        x-data x-on:keydown.escape.window="$wire.set('confirmingPurge', false)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center gap-3 px-6 py-4 border-b border-[#f1f5f9]">
                <span class="flex items-center justify-center w-9 h-9 rounded-full bg-rose-100">
                    <i class="bx bx-trash text-rose-600 text-lg"></i>
                </span>
                <div>
                    <h3 class="text-[15px] font-semibold text-[#0f172a]">Purge Old Audit Logs</h3>
                    <p class="text-[12px] text-[#94a3b8]">This action is permanent and cannot be undone.</p>
                </div>
                <button wire:click="$set('confirmingPurge', false)" class="ml-auto text-[#94a3b8] hover:text-[#475569]">
                    <i class="bx bx-x text-xl"></i>
                </button>
            </div>

            {{-- Body --}}
            <div class="px-6 py-5 space-y-4">

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-1.5">
                        Delete logs older than
                    </label>
                    <x-form.select wire:model.live="purgeMonths">
                        @foreach ($monthsOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </x-form.select>
                </div>

                <label class="flex items-start gap-3 cursor-pointer select-none">
                    <input type="checkbox" wire:model.live="protectSyllabusLogs"
                        class="mt-0.5 w-4 h-4 rounded border-[#cbd5e1] text-emerald-600 focus:ring-emerald-500">
                    <div>
                        <span class="text-[13px] font-medium text-[#0f172a]">Preserve Syllabus &amp; Course logs</span>
                        <p class="text-[12px] text-[#94a3b8] mt-0.5">Keeps all logs for the Syllabus and Course modules regardless of age — recommended for academic accountability.</p>
                    </div>
                </label>

                <div class="rounded-xl px-4 py-3 {{ $purgePreviewCount > 0 ? 'bg-rose-50 border border-rose-200' : 'bg-[#f8fafc] border border-[#e2e8f0]' }}">
                    <p class="text-[13px] font-medium {{ $purgePreviewCount > 0 ? 'text-rose-700' : 'text-[#475569]' }}">
                        @if ($purgePreviewCount === 0)
                            <i class="bx bx-check-circle mr-1"></i> No logs match this criteria.
                        @else
                            <i class="bx bx-error-circle mr-1"></i>
                            <span class="font-bold">{{ number_format($purgePreviewCount) }}</span>
                            {{ $purgePreviewCount === 1 ? 'entry' : 'entries' }} will be permanently deleted.
                        @endif
                    </p>
                </div>

            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-[#f1f5f9] bg-[#f8fafc]">
                <button wire:click="$set('confirmingPurge', false)" type="button"
                    class="px-4 py-2 text-[13px] font-medium text-[#475569] bg-white border border-[#e2e8f0] rounded-lg hover:bg-[#f1f5f9] transition">
                    Cancel
                </button>
                <button wire:click="executePurge" type="button"
                    @disabled($purgePreviewCount === 0)
                    class="px-4 py-2 text-[13px] font-semibold text-white rounded-lg transition
                        {{ $purgePreviewCount > 0
                            ? 'bg-rose-600 hover:bg-rose-700'
                            : 'bg-rose-200 cursor-not-allowed' }}">
                    <span wire:loading.remove wire:target="executePurge">
                        <i class="bx bx-trash mr-1"></i> Purge {{ number_format($purgePreviewCount) }} {{ $purgePreviewCount === 1 ? 'Entry' : 'Entries' }}
                    </span>
                    <span wire:loading wire:target="executePurge">
                        <i class="bx bx-loader-alt animate-spin mr-1"></i> Purging…
                    </span>
                </button>
            </div>

        </div>
    </div>
@endif