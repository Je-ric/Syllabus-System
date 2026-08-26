@props([
    'paginator' => null,
    'onFirstPage' => null,
    'hasMorePages' => null,
    'previousPageUrl' => null,
    'nextPageUrl' => null,
    'currentPage' => null,
    'lastPage' => null,
    'urlRange' => null,
    'showCount' => true,
])

@php
    // Use provided values or fall back to paginator object methods
    $onFirstPage = $onFirstPage ?? $paginator?->onFirstPage() ?? true;
    $hasMorePages = $hasMorePages ?? $paginator?->hasMorePages() ?? false;
    $previousPageUrl = $previousPageUrl ?? $paginator?->previousPageUrl();
    $nextPageUrl = $nextPageUrl ?? $paginator?->nextPageUrl();
    $currentPage = $currentPage ?? $paginator?->currentPage() ?? 1;
    $lastPage = $lastPage ?? $paginator?->lastPage() ?? 1;
    
    // Calculate limited page range (show max 3 pages centered around current page)
    $urlRange = [];
    $showEllipsisAfter = false;
    $showLastPage = false;
    
    if ($paginator) {
        $maxPagesToShow = 3;
        if ($lastPage <= $maxPagesToShow) {
            // Show all pages if total is less than or equal to max
            $urlRange = $paginator->getUrlRange(1, $lastPage);
        } else {
            // Calculate range centered around current page
            $half = floor($maxPagesToShow / 2);
            $startPage = max(1, $currentPage - $half);
            $endPage = min($lastPage, $startPage + $maxPagesToShow - 1);
            
            // Adjust if we're near the end
            if ($endPage - $startPage < $maxPagesToShow - 1) {
                $startPage = max(1, $endPage - $maxPagesToShow + 1);
            }
            
            $urlRange = $paginator->getUrlRange($startPage, $endPage);
            
            // Determine if we need ellipsis and last page
            $showEllipsisAfter = $endPage < $lastPage;
            $showLastPage = $endPage < $lastPage;
        }
    }
@endphp

@if($paginator && $paginator->hasPages())
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mt-8">
        {{-- Count on the left --}}
        @if($showCount)
            <p class="text-[13px] text-[#71717a]">
                Showing {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ $paginator->total() }}
            </p>
        @endif

        {{-- Pagination controls on the right --}}
        <nav aria-label="Pagination" class="flex space-x-2 justify-center md:justify-end">
            {{-- Previous --}}
            @if($onFirstPage)
                <a href="#" aria-disabled="true" tabindex="-1" aria-label="Previous page"
                    class="flex items-center justify-center shrink-0 bg-gray-100 w-9 h-9 rounded-md cursor-default focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="fill-slate-400 size-3 rotate-180 overflow-visible" viewBox="0 0 451.846 451.847"
                        aria-hidden="true">
                        <path
                            d="M345.441 248.292 151.154 442.573c-12.359 12.365-32.397 12.365-44.75 0-12.354-12.354-12.354-32.391 0-44.744L278.318 225.92 106.409 54.017c-12.354-12.359-12.354-32.394 0-44.748 12.354-12.359 32.391-12.359 44.75 0l194.287 194.284c6.177 6.18 9.262 14.271 9.262 22.366 0 8.099-3.091 16.196-9.267 22.373"
                            data-original="#000000" />
                    </svg>
                </a>
            @else
                <a href="{{ $previousPageUrl }}" aria-label="Previous page"
                    class="flex items-center justify-center shrink-0 bg-gray-200 w-9 h-9 rounded-md hover:bg-gray-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="fill-slate-600 size-3 rotate-180 overflow-visible" viewBox="0 0 451.846 451.847"
                        aria-hidden="true">
                        <path
                            d="M345.441 248.292 151.154 442.573c-12.359 12.365-32.397 12.365-44.75 0-12.354-12.354-12.354-32.391 0-44.744L278.318 225.92 106.409 54.017c-12.354-12.359-12.354-32.394 0-44.748 12.354-12.359 32.391-12.359 44.75 0l194.287 194.284c6.177 6.18 9.262 14.271 9.262 22.366 0 8.099-3.091 16.196-9.267 22.373"
                            data-original="#000000" />
                    </svg>
                </a>
            @endif

            {{-- Page numbers --}}
            @foreach($urlRange as $page => $url)
                @if($page == $currentPage)
                    <a href="#" aria-current="page"
                        class="flex items-center justify-center shrink-0 text-sm font-semibold text-white w-9 h-9 rounded-md bg-emerald-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                        {{ $page }}
                    </a>
                @elseif($url)
                    <a href="{{ $url }}"
                        class="flex items-center justify-center shrink-0 text-sm font-semibold text-slate-900 w-9 h-9 rounded-md hover:bg-gray-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                        {{ $page }}
                    </a>
                @else
                    <span aria-hidden="true"
                        class="flex items-center justify-center shrink-0 text-sm font-semibold text-slate-900 w-9 h-9 rounded-md">
                        ...
                    </span>
                @endif
            @endforeach
            
            @if($showEllipsisAfter)
                <span aria-hidden="true"
                    class="flex items-center justify-center shrink-0 text-sm font-semibold text-slate-900 w-9 h-9 rounded-md">
                    ...
                </span>
            @endif
            
            @if($showLastPage)
                <a href="{{ $paginator->url($lastPage) }}"
                    class="flex items-center justify-center shrink-0 text-sm font-semibold text-slate-900 w-9 h-9 rounded-md hover:bg-gray-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                    {{ $lastPage }}
                </a>
            @endif

            {{-- Next --}}
            @if($hasMorePages)
                <a href="{{ $nextPageUrl }}" aria-label="Next page"
                    class="flex items-center justify-center shrink-0 bg-gray-200 w-9 h-9 rounded-md hover:bg-gray-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="fill-slate-600 size-3 overflow-visible"
                        viewBox="0 0 451.846 451.847" aria-hidden="true">
                        <path
                            d="M345.441 248.292 151.154 442.573c-12.359 12.365-32.397 12.365-44.75 0-12.354-12.354-12.354-32.391 0-44.744L278.318 225.92 106.409 54.017c-12.354-12.359-12.354-32.394 0-44.748 12.354-12.359 32.391-12.359 44.75 0l194.287 194.284c6.177 6.18 9.262 14.271 9.262 22.366 0 8.099-3.091 16.196-9.267 22.373"
                            data-original="#000000" />
                    </svg>
                </a>
            @else
                <a href="#" aria-disabled="true" tabindex="-1" aria-label="Next page"
                    class="flex items-center justify-center shrink-0 bg-gray-100 w-9 h-9 rounded-md cursor-default focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="fill-slate-400 size-3 overflow-visible"
                        viewBox="0 0 451.846 451.847" aria-hidden="true">
                        <path
                            d="M345.441 248.292 151.154 442.573c-12.359 12.365-32.397 12.365-44.75 0-12.354-12.354-12.354-32.391 0-44.744L278.318 225.92 106.409 54.017c-12.354-12.359-12.354-32.394 0-44.748 12.354-12.359 32.391-12.359 44.75 0l194.287 194.284c6.177 6.18 9.262 14.271 9.262 22.366 0 8.099-3.091 16.196-9.267 22.373"
                            data-original="#000000" />
                    </svg>
                </a>
            @endif
        </nav>
    </div>
@endif