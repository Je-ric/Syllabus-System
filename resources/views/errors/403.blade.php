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
                    <div class="w-24 h-24 rounded-full bg-rose-100 flex items-center justify-center">
                        <i class="bx bx-shield-x text-5xl text-rose-500"></i>
                    </div>
                </div>

                <h1 class="text-6xl font-bold text-slate-800 mb-2">403</h1>
                <h2 class="text-2xl font-semibold text-slate-700 mb-4">Access Denied</h2>

                <p class="text-slate-500 mb-8 max-w-md mx-auto">
                    You don't have permission to access this page. Please contact your administrator if you believe this is an error.
                </p>

                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('dashboard') }}"
                       class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-semibold text-white transition-colors"
                       style="background: var(--clsu-green);"
                       onmouseover="this.style.background='var(--clsu-cobra)'"
                       onmouseout="this.style.background='var(--clsu-green)'">
                        <i class="bx bx-home-alt text-lg"></i>
                        Return to Dashboard
                    </a>
                    <button onclick="history.back()"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-semibold text-slate-600 border border-slate-300 bg-white hover:bg-slate-50 transition-colors">
                        <i class="bx bx-arrow-back text-lg"></i>
                        Go Back
                    </button>
                </div>
            </div>

            <div class="bg-slate-50 px-8 py-4 border-t border-slate-200">
                <p class="text-xs text-slate-400">
                    If you need access to this resource, please contact your system administrator.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
