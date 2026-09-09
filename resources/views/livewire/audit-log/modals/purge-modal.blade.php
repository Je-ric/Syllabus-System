@props([
    'confirmingPurge' => false,
    'purgeMonths' => 6,
    'protectSyllabusLogs' => true,
    'purgePreviewCount' => 0,
])

@php
    $monthsOptions = [
        0 => 'All time',
        3 => '3 months',
        6 => '6 months',
        12 => '1 year',
        24 => '2 years',
    ];
    
    $alertMessage = $purgePreviewCount === 0 
        ? 'No logs match this criteria.' 
        : number_format($purgePreviewCount) . ' ' . ($purgePreviewCount === 1 ? 'entry' : 'entries') . ' will be permanently deleted.';
    
    $alertType = $purgePreviewCount > 0 ? 'error' : 'success';
    
    $alertStyles = [
        'success' => 'border-[#00965F] bg-[#F4FFFA] text-[#06754E]',
        'error' => 'border-[#E52F28] bg-[#FFF8F8] text-[#731814]',
    ];
    
    $alertContainerStyle = $alertStyles[$alertType] ?? $alertStyles['success'];
    
    $alertIconStyles = [
        'success' => 'bg-[#D5FFF0] text-[#06754E] ring-1 ring-inset ring-[#AEFFE2]',
        'error' => 'bg-[#FFE3E2] text-[#D21B14] ring-1 ring-inset ring-[#FFA2A2]',
    ];
    
    $alertIconStyle = $alertIconStyles[$alertType] ?? $alertIconStyles['success'];
    
    $alertIcons = [
        'success' => 'bx-check-circle',
        'error' => 'bx-error-circle',
    ];
    
    $alertIcon = $alertIcons[$alertType] ?? $alertIcons['success'];
@endphp

@if ($confirmingPurge)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
        x-data x-on:keydown.escape.window="$wire.set('confirmingPurge', false)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden">
            
            <div class="absolute inset-x-0 top-0 h-[2.5px] rounded-t-2xl pointer-events-none z-10" style="background:linear-gradient(90deg,#E52F28 0%,#F45855 55%,rgba(229,47,40,0) 100%);" aria-hidden="true"></div>
            
            <header class="px-5 py-4 border-b border-[#E3E8EB] bg-white shrink-0">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <span class="flex items-center justify-center w-9 h-9 rounded-[10px] shrink-0 border" style="background:#D21B14; border-color:#FFA2A2;">
                            <i class="bx bx-trash text-[15px] leading-none" style="color:#FFE3E2;"></i>
                        </span>
                        <div class="flex-1 min-w-0 text-[14.5px] font-bold text-[#253540]">
                            <h3 class="text-[15px] font-semibold text-[#0f172a]">Purge Old Audit Logs</h3>
                            <p class="text-[12px] text-[#94a3b8]">This action is permanent and cannot be undone.</p>
                        </div>
                    </div>
                    <button type="button" wire:click="$set('confirmingPurge', false)" class="shrink-0 rounded-lg p-1.5 text-[#A5B2BD] hover:text-[#394056] hover:bg-[#F1F3F5] active:scale-90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#00C075]/30 transition-all duration-150" aria-label="Close">
                        <i class="bx bx-x text-xl leading-none"></i>
                    </button>
                </div>
            </header>

            <div class="flex-1 min-h-0 overflow-y-auto p-6">
                <div class="space-y-4">

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-1.5">
                            Delete logs older than
                        </label>
                        <div class="relative">
                            <select wire:model.live="purgeMonths"
                                class="w-full appearance-none rounded-[14px] bg-white border border-[#d4d4d8] px-3.5 py-2.5 pr-9 text-[14px] text-[#09090b] hover:border-[#a1a1aa] focus:border-[#16a34a] focus:outline-none focus:ring-2 focus:ring-[#16a34a]/15 disabled:bg-[#f4f4f5] disabled:text-[#a1a1aa] disabled:cursor-not-allowed disabled:border-[#e4e4e7] transition-colors duration-150">
                                @foreach ($monthsOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5 text-[#a1a1aa]">
                                <i class="bx bx-chevron-down text-base leading-none"></i>
                            </span>
                        </div>
                    </div>

                    <label class="flex items-start gap-3 cursor-pointer select-none">
                        <input type="checkbox" wire:model.live="protectSyllabusLogs"
                            class="mt-0.5 h-4 w-4 rounded-sm border-[#d4d4d8] text-[#16a34a]
                                   focus:ring-2 focus:ring-[#16a34a]/20 focus:ring-offset-0
                                   transition-colors">
                        <div>
                            <span class="text-[13px] font-medium text-[#0f172a]">Preserve Syllabus &amp; Course logs</span>
                            <p class="text-[12px] text-[#94a3b8] mt-0.5">Keeps all logs for the Syllabus and Course modules regardless of age — recommended for academic accountability.</p>
                        </div>
                    </label>

                    <div class="rounded-[10px] border p-3.5 {{ $alertContainerStyle }}" role="alert">
                        <div class="flex items-start gap-3">
                            <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-[8px] {{ $alertIconStyle }}">
                                <i class="bx {{ $alertIcon }} text-base leading-none"></i>
                            </span>
                            <div class="min-w-0 flex-1 pt-0.5">
                                <div class="text-[13px] leading-relaxed opacity-90">
                                    <p>{{ $alertMessage }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <footer class="border-t border-[#F1F3F5] bg-[#F9FAFA] px-5 py-3.5 flex gap-3 shrink-0 justify-end">
                <button type="button" wire:click="$set('confirmingPurge', false)"
                    class="inline-flex items-center gap-2 px-4 py-2 text-[13.5px] font-semibold rounded-[7px] bg-white text-[#394056] border border-[#D6DDE3] hover:bg-[#F1F3F5] hover:border-[#C1C8D4] focus:ring-[#D6DDE3]/20 transition-all duration-200 active:scale-[0.97] focus:ring-2 focus:outline-none disabled:opacity-40 disabled:pointer-events-none">
                    Cancel
                </button>
                <button type="button" wire:click="executePurge"
                    @disabled($purgePreviewCount === 0)
                    {{ $purgePreviewCount === 0 ? 'disabled' : '' }}
                    class="inline-flex items-center gap-2 px-4 py-2 text-[13.5px] font-semibold rounded-[7px] text-white bg-[linear-gradient(180deg,#E52F28_0%,#BA1F19_100%)] hover:bg-[linear-gradient(180deg,#D21B14_0%,#9D1F1A_100%)] active:bg-[#9D1F1A] focus:ring-[#E52F28]/35 shadow-[0_1px_3px_rgba(186,31,25,0.35)] transition-all duration-200 active:scale-[0.97] focus:ring-2 focus:outline-none disabled:opacity-40 disabled:pointer-events-none">
                    <span wire:loading.remove wire:target="executePurge">
                        <i class="bx bx-trash"></i> Purge {{ number_format($purgePreviewCount) }} {{ $purgePreviewCount === 1 ? 'Entry' : 'Entries' }}
                    </span>
                    <span wire:loading wire:target="executePurge">
                        <i class="bx bx-loader-alt animate-spin"></i> Purging…
                    </span>
                </button>
            </footer>
        </div>
    </div>
@endif