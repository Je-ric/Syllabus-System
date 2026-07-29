{{-- Layout partial: session expiry modal — shown on Livewire 419 (CSRF/session expired) --}}

<div id="session-expired-modal"
     class="hidden fixed inset-0 z-[9999] flex items-center justify-center"
     role="alertdialog"
     aria-modal="true"
     aria-labelledby="session-expired-title"
     aria-describedby="session-expired-desc">

    <div class="absolute inset-0 bg-[#09090b]/50 backdrop-blur-[3px]"></div>

    <div class="relative bg-white rounded-[16px] border border-[#e4e4e7] p-6 w-80 text-center"
         style="box-shadow: 0 8px 40px rgba(0,0,0,0.14);">
        <div class="flex items-center justify-center w-12 h-12 rounded-full
                    bg-amber-50 border border-amber-200 mx-auto mb-4">
            <i class="bx bx-time-five text-2xl text-amber-500" aria-hidden="true"></i>
        </div>
        <h3 id="session-expired-title" class="text-[14px] font-bold text-[#09090b] mb-1">
            Session Expired
        </h3>
        <p id="session-expired-desc" class="text-[12px] text-[#71717a] mb-4">
            Your session has expired. Any unsaved content in open editors may be lost.
            Please save your work, then refresh the page to continue.
        </p>
        <button onclick="window.location.reload()"
            class="w-full px-4 py-2 rounded-[10px] bg-[#09090b] text-white
                   text-[13px] font-semibold hover:bg-[#18181b] transition">
            Refresh &amp; Log In
        </button>
    </div>
</div>

<script>
    document.addEventListener('livewire:request-error', (e) => {
        if (e.detail?.status === 419) {
            document.getElementById('session-expired-modal')?.classList.remove('hidden');
        }
    });
</script>
