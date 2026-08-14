<!DOCTYPE html>
<html lang="en">
<head>
    @include('includes.head-assets')
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen bg-linear-to-br from-slate-50 to-slate-100 flex items-center justify-center p-4">
    <div class="max-w-2xl w-full text-center">
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-200">
            <div class="p-8 md:p-12">
                <div class="flex justify-center mb-6">
                    <div class="w-24 h-24 rounded-full bg-purple-100 flex items-center justify-center">
                        <i class="bx bx-tachometer text-5xl text-purple-500"></i>
                    </div>
                </div>
                
                <h1 class="text-6xl font-bold text-slate-800 mb-2">429</h1>
                <h2 class="text-2xl font-semibold text-slate-700 mb-4">Too Many Requests</h2>
                
                <p class="text-slate-500 mb-8 max-w-md mx-auto">
                    You've made too many requests in a short period. Please wait a moment before trying again.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <button onclick="window.location.reload()" 
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-semibold text-white transition-colors"
                            style="background: var(--clsu-green);"
                            onmouseover="this.style.background='var(--clsu-cobra)'"
                            onmouseout="this.style.background='var(--clsu-green)'">
                        <i class="bx bx-refresh text-lg"></i>
                        Try Again
                    </button>
                    <a href="{{ route('dashboard') }}" 
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-semibold text-slate-600 border border-slate-300 bg-white hover:bg-slate-50 transition-colors">
                        <i class="bx bx-home-alt text-lg"></i>
                        Return to Dashboard
                    </a>
                </div>
            </div>
            
            <div class="bg-slate-50 px-8 py-4 border-t border-slate-200">
                <p class="text-xs text-slate-400">
                    This is a rate limiting measure to protect system stability.
                </p>
            </div>
        </div>
    </div>
</body>
</html>