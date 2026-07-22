<div>
    {{-- PO × PEO Mapping Matrix --}}
    @if(count($peos) === 0 || count($pos) === 0)
        <div class="rounded-2xl border-2 border-dashed border-[#E3E8EB] bg-[#F9FAFA] py-14 text-center">
            <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl mb-3"
                  style="background:#F1F3F5;">
                <i class="bx bx-grid-alt text-3xl" style="color:#C1C8D4;"></i>
            </span>
            <p class="mt-2 text-[13px] font-semibold text-[#72809E]">No matrix to display</p>
            <p class="text-[12px] text-[#A5B2BD] mt-0.5">Add PEOs and POs first, then map them in the PO tab.</p>
        </div>
    @else
        {{-- Legend --}}
        <div class="flex flex-wrap items-center gap-4 mb-4 px-1">
            <span class="flex items-center gap-1.5 text-[12px]" style="color:#72809E;">
                <span class="inline-flex items-center justify-center w-5 h-5 rounded-[6px] border"
                      style="background:#D5FFF0; border-color:#AEFFE2;">
                    <i class="bx bx-check text-xs" style="color:#00965F;"></i>
                </span>
                Mapped
            </span>
            <span class="flex items-center gap-1.5 text-[12px]" style="color:#72809E;">
                <span class="inline-flex items-center justify-center w-5 h-5 rounded-[6px] border"
                      style="background:#F1F3F5; border-color:#E3E8EB;">
                    <i class="bx bx-minus text-xs" style="color:#C1C8D4;"></i>
                </span>
                Not mapped
            </span>
            <span class="ml-auto text-[11px] italic" style="color:#A5B2BD;">Read-only view. Edit mappings in the POs tab.</span>
        </div>

        {{-- Scrollable matrix --}}
        <div class="overflow-x-auto rounded-2xl border border-[#E3E8EB]" style="box-shadow:0 1px 8px rgba(16,24,40,0.06);">
            <table class="w-full text-[12px] border-collapse">
                <thead>
                    <tr style="background:#F9FAFA; border-bottom:1px solid #E3E8EB;">
                        {{-- Corner cell --}}
                        <th class="sticky left-0 z-10 px-4 py-3 text-left min-w-55 border-r"
                            style="background:#F9FAFA; border-color:#E3E8EB;">
                            <div class="flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-widest" style="color:#A5B2BD;">
                                <i class="bx bx-target-lock text-sm"></i>
                                PO
                                <span class="mx-1" style="color:#D6DDE3;">/</span>
                                <i class="bx bx-graduation text-sm"></i>
                                PEO
                            </div>
                        </th>
                        @foreach($peos as $peo)
                            <th class="px-3 py-3 text-center font-bold whitespace-nowrap border-r last:border-r-0 min-w-16"
                                style="color:#394056; border-color:#F1F3F5;">
                                <div class="flex flex-col items-center gap-1">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-[8px] font-bold text-[11px] border"
                                          style="background:#D5FFF0; color:#06754E; border-color:#AEFFE2;">
                                        {{ strtoupper($peo['peo_code']) }}
                                    </span>
                                </div>
                            </th>
                        @endforeach
                        <th class="px-3 py-3 text-center font-bold whitespace-nowrap min-w-14" style="color:#A5B2BD;">
                            <div class="flex flex-col items-center gap-1">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-[8px] border"
                                      style="background:#F1F3F5; border-color:#E3E8EB;">
                                    <i class="bx bx-link text-sm" style="color:#93A1AF;"></i>
                                </span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pos as $poIndex => $po)
                        @php $mappedPeoIds = $mapping[$po['id']] ?? []; @endphp
                        <tr class="border-b last:border-b-0 transition-colors group"
                            style="background:{{ $poIndex % 2 === 0 ? '#FFFFFF' : '#FAFDFB' }}; border-color:#F1F3F5;"
                            x-data="{}" @mouseenter="$el.style.background='#EDFFF8'" @mouseleave="$el.style.background='{{ $poIndex % 2 === 0 ? '#FFFFFF' : '#FAFDFB' }}'">
                            {{-- PO label --}}
                            <td class="sticky left-0 z-10 px-4 py-3 border-r" style="background:inherit; border-color:#E3E8EB;">
                                <div class="flex items-start gap-2.5">
                                    <span class="shrink-0 inline-flex items-center justify-center w-7 h-7 rounded-[6px] text-[10px] font-bold mt-0.5 border"
                                          style="background:#DAF1FF; color:#194C6E; border-color:#AEDFFF;">
                                        {{ strtoupper($po['po_code']) }}
                                    </span>
                                    <p class="text-[12px] leading-relaxed line-clamp-2" style="color:#394056;">{{ $po['po_text'] }}</p>
                                </div>
                            </td>
                            {{-- Mapping cells --}}
                            @foreach($peos as $peo)
                                <td class="px-3 py-3 text-center border-r last:border-r-0" style="border-color:#F1F3F5;">
                                    @if(in_array($peo['id'], $mappedPeoIds))
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-[6px] border"
                                              style="background:#D5FFF0; border-color:#AEFFE2;">
                                            <i class="bx bx-check text-sm" style="color:#00965F;"></i>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-[6px] border"
                                              style="background:#F9FAFA; border-color:#E3E8EB;">
                                            <i class="bx bx-minus text-xs" style="color:#C1C8D4;"></i>
                                        </span>
                                    @endif
                                </td>
                            @endforeach
                            {{-- Count --}}
                            <td class="px-3 py-3 text-center">
                                @php $count = count($mappedPeoIds); @endphp
                                <span class="inline-flex items-center justify-center min-w-[22px] h-[22px] px-1.5 rounded-full text-[10px] font-bold border"
                                      style="{{ $count > 0 ? 'background:#D5FFF0; color:#06754E; border-color:#AEFFE2;' : 'background:#F1F3F5; color:#A5B2BD; border-color:#E3E8EB;' }}">
                                    {{ $count }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                {{-- PEO coverage footer --}}
                <tfoot>
                    <tr style="border-top:2px solid #E3E8EB; background:#F9FAFA;">
                        <td class="sticky left-0 z-10 px-4 py-2.5 border-r" style="background:#F9FAFA; border-color:#E3E8EB;">
                            <span class="text-[11px] font-bold uppercase tracking-widest flex items-center gap-1.5" style="color:#A5B2BD;">
                                <i class="bx bx-bar-chart-alt-2 text-sm"></i>
                                POs per PEO
                            </span>
                        </td>
                        @foreach($peos as $peo)
                            @php
                                $poCount = collect($mapping)->filter(fn($ids) => in_array($peo['id'], $ids))->count();
                            @endphp
                            <td class="px-3 py-2.5 text-center border-r last:border-r-0" style="border-color:#F1F3F5;">
                                <span class="inline-flex items-center justify-center min-w-[22px] h-[22px] px-1.5 rounded-full text-[10px] font-bold border"
                                      style="{{ $poCount > 0 ? 'background:#DAF1FF; color:#194C6E; border-color:#AEDFFF;' : 'background:#F1F3F5; color:#A5B2BD; border-color:#E3E8EB;' }}">
                                    {{ $poCount }}
                                </span>
                            </td>
                        @endforeach
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Summary row --}}
        <div class="flex flex-wrap gap-3 mt-4 px-1">
            <div class="flex items-center gap-2 px-3 py-2 rounded-[10px] border" style="background:#EDFFF8; border-color:#AEFFE2;">
                <i class="bx bx-graduation text-base" style="color:#00965F;"></i>
                <span class="text-[12px] font-semibold" style="color:#06754E;">{{ count($peos) }} PEO{{ count($peos) !== 1 ? 's' : '' }}</span>
            </div>
            <div class="flex items-center gap-2 px-3 py-2 rounded-[10px] border" style="background:#DAF1FF; border-color:#AEDFFF;">
                <i class="bx bx-target-lock text-base" style="color:#3197D6;"></i>
                <span class="text-[12px] font-semibold" style="color:#194C6E;">{{ count($pos) }} PO{{ count($pos) !== 1 ? 's' : '' }}</span>
            </div>
            @php
                $totalMappings = collect($mapping)->sum(fn($ids) => count($ids));
                $totalPossible = count($peos) * count($pos);
            @endphp
            <div class="flex items-center gap-2 px-3 py-2 rounded-[10px] border" style="background:#F9FAFA; border-color:#E3E8EB;">
                <i class="bx bx-link text-base" style="color:#72809E;"></i>
                <span class="text-[12px] font-semibold" style="color:#394056;">{{ $totalMappings }} / {{ $totalPossible }} mappings</span>
            </div>
        </div>
    @endif
</div>
