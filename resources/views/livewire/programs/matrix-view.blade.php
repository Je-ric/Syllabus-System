<div>
    {{-- PO × PEO Mapping Matrix --}}
    @if(count($peos) === 0 || count($pos) === 0)
        <div class="rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 py-14 text-center">
            <i class="bx bx-grid-alt text-4xl text-slate-300"></i>
            <p class="mt-2 text-[13px] font-semibold text-slate-500">No matrix to display</p>
            <p class="text-[12px] text-slate-400 mt-0.5">Add PEOs and POs first, then map them in the PO tab.</p>
        </div>
    @else
        {{-- Legend --}}
        <div class="flex flex-wrap items-center gap-4 mb-4 px-1">
            <span class="flex items-center gap-1.5 text-[12px] text-slate-500">
                <span class="inline-flex items-center justify-center w-5 h-5 rounded bg-emerald-100 ring-1 ring-emerald-300">
                    <i class="bx bx-check text-emerald-700 text-xs"></i>
                </span>
                Mapped
            </span>
            <span class="flex items-center gap-1.5 text-[12px] text-slate-500">
                <span class="inline-flex items-center justify-center w-5 h-5 rounded bg-slate-100 ring-1 ring-slate-200">
                    <span class="text-slate-300 text-xs">—</span>
                </span>
                Not mapped
            </span>
            <span class="ml-auto text-[11px] text-slate-400 italic">Read-only view. Edit mappings in the POs tab.</span>
        </div>

        {{-- Scrollable matrix --}}
        <div class="overflow-x-auto rounded-xl border border-slate-200" style="box-shadow:0 1px 8px rgba(0,0,0,.06);">
            <table class="w-full text-[12px] border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        {{-- Corner cell --}}
                        <th class="sticky left-0 z-10 bg-slate-50 px-4 py-3 text-left min-w-55 border-r border-slate-200">
                            <div class="flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-widest text-slate-400">
                                <i class="bx bx-target-lock text-sm"></i>
                                PO
                                <span class="mx-1 text-slate-300">/</span>
                                <i class="bx bx-graduation text-sm"></i>
                                PEO
                            </div>
                        </th>
                        @foreach($peos as $peo)
                            <th class="px-3 py-3 text-center font-bold text-slate-600 whitespace-nowrap border-r border-slate-100 last:border-r-0 min-w-16">
                                <div class="flex flex-col items-center gap-1">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 ring-1 ring-emerald-200 font-bold text-[11px]">
                                        {{ strtoupper($peo['peo_code']) }}
                                    </span>
                                </div>
                            </th>
                        @endforeach
                        <th class="px-3 py-3 text-center font-bold text-slate-500 whitespace-nowrap min-w-14">
                            <div class="flex flex-col items-center gap-1">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 text-slate-500 ring-1 ring-slate-200 text-[10px] font-bold">
                                    <i class="bx bx-link text-sm"></i>
                                </span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pos as $poIndex => $po)
                        @php $mappedPeoIds = $mapping[$po['id']] ?? []; @endphp
                        <tr class="border-b border-slate-100 last:border-b-0 {{ $poIndex % 2 === 0 ? 'bg-white' : 'bg-slate-50/40' }} hover:bg-emerald-50/20 transition-colors group">
                            {{-- PO label --}}
                            <td class="sticky left-0 z-10 px-4 py-3 border-r border-slate-200 {{ $poIndex % 2 === 0 ? 'bg-white' : 'bg-slate-50/40' }} group-hover:bg-emerald-50/20">
                                <div class="flex items-start gap-2.5">
                                    <span class="shrink-0 inline-flex items-center justify-center w-7 h-7 rounded-md bg-blue-100 text-blue-800 ring-1 ring-blue-200 text-[10px] font-bold mt-0.5">
                                        {{ strtoupper($po['po_code']) }}
                                    </span>
                                    <p class="text-[12px] text-slate-600 leading-relaxed line-clamp-2">{{ $po['po_text'] }}</p>
                                </div>
                            </td>
                            {{-- Mapping cells --}}
                            @foreach($peos as $peo)
                                <td class="px-3 py-3 text-center border-r border-slate-100 last:border-r-0">
                                    @if(in_array($peo['id'], $mappedPeoIds))
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-md bg-emerald-100 ring-1 ring-emerald-300">
                                            <i class="bx bx-check text-emerald-700 text-sm"></i>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-md bg-slate-50 ring-1 ring-slate-200">
                                            <span class="text-slate-300 text-[10px] font-bold">—</span>
                                        </span>
                                    @endif
                                </td>
                            @endforeach
                            {{-- Count --}}
                            <td class="px-3 py-3 text-center">
                                @php $count = count($mappedPeoIds); @endphp
                                <span class="inline-flex items-center justify-center min-w-[1.4rem] h-5 px-1.5 rounded-full text-[10px] font-bold
                                    {{ $count > 0 ? 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200' : 'bg-slate-100 text-slate-400' }}">
                                    {{ $count }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                {{-- PEO coverage footer --}}
                <tfoot>
                    <tr class="border-t-2 border-slate-200 bg-slate-50">
                        <td class="sticky left-0 z-10 bg-slate-50 px-4 py-2.5 border-r border-slate-200">
                            <span class="text-[11px] font-bold uppercase tracking-widest text-slate-400 flex items-center gap-1.5">
                                <i class="bx bx-bar-chart-alt-2 text-sm"></i>
                                POs per PEO
                            </span>
                        </td>
                        @foreach($peos as $peo)
                            @php
                                $poCount = collect($mapping)->filter(fn($ids) => in_array($peo['id'], $ids))->count();
                            @endphp
                            <td class="px-3 py-2.5 text-center border-r border-slate-100 last:border-r-0">
                                <span class="inline-flex items-center justify-center min-w-[1.4rem] h-5 px-1.5 rounded-full text-[10px] font-bold
                                    {{ $poCount > 0 ? 'bg-blue-100 text-blue-700 ring-1 ring-blue-200' : 'bg-slate-100 text-slate-400' }}">
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
            <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-emerald-50 border border-emerald-100">
                <i class="bx bx-graduation text-emerald-600 text-base"></i>
                <span class="text-[12px] text-emerald-700 font-semibold">{{ count($peos) }} PEO{{ count($peos) !== 1 ? 's' : '' }}</span>
            </div>
            <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-blue-50 border border-blue-100">
                <i class="bx bx-target-lock text-blue-600 text-base"></i>
                <span class="text-[12px] text-blue-700 font-semibold">{{ count($pos) }} PO{{ count($pos) !== 1 ? 's' : '' }}</span>
            </div>
            @php
                $totalMappings = collect($mapping)->sum(fn($ids) => count($ids));
                $totalPossible = count($peos) * count($pos);
            @endphp
            <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-50 border border-slate-200">
                <i class="bx bx-link text-slate-500 text-base"></i>
                <span class="text-[12px] text-slate-600 font-semibold">{{ $totalMappings }} / {{ $totalPossible }} mappings</span>
            </div>
        </div>
    @endif
</div>
