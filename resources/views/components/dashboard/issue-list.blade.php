@props([
    'issues',
    'title',
    'emptyTitle' => 'All clear',
    'emptyMessage' => 'No issues found in this area.',
    'type' => 'warning',
])

<x-layout.card-section :title="$title" icon="bx-error-circle" :count="count($issues) ?: null">
    @if (count($issues) === 0)
        <x-feedback-status.empty-state
            icon="bx-check-circle"
            :title="$emptyTitle"
            :message="$emptyMessage" />
    @else
        <div class="space-y-3">
            @foreach ($issues as $issue)
                <div class="rounded-[10px] border border-[#E3E8EB] bg-[#FAFAFA] p-3.5">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-[13px] font-semibold text-[#394056]">{{ $issue['label'] }}</p>
                            @if (! empty($issue['samples']))
                                <ul class="mt-2 space-y-1">
                                    @foreach ($issue['samples'] as $sample)
                                        <li class="text-[12px] text-[#72809E] truncate">• {{ $sample }}</li>
                                    @endforeach
                                </ul>
                                @if ($issue['count'] > count($issue['samples']))
                                    <p class="mt-1.5 text-[11px] text-[#93A1AF]">
                                        + {{ $issue['count'] - count($issue['samples']) }} more
                                    </p>
                                @endif
                            @endif
                        </div>
                        <span class="shrink-0 inline-flex items-center justify-center min-w-[28px] h-7 px-2 rounded-full text-[11px] font-bold
                            {{ $type === 'mapping' ? 'bg-[#FFE3E2] text-[#731814] border border-[#FFA2A2]' : 'bg-[#FFF6E2] text-[#875200] border border-[#FFE9B5]' }}">
                            {{ number_format($issue['count']) }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-layout.card-section>
