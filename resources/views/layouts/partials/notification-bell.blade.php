{{--
    Notification Bell Partial
    ─────────────────────────
    Included by topbar.blade.php. No Livewire — pure Alpine + fetch.

    HOW TO SEND A NOTIFICATION (from any Controller or Livewire component):
      use App\Notifications\SyllabusStatusNotification;
      $recipient->notify(new SyllabusStatusNotification($syllabus, 'submitted'));

    STATUSES: 'submitted' | 'approved' | 'for_revision' | 'concurred' | 'rejected'
--}}

<div
    x-data="notificationBell({{ (int) $initialUnreadCount }})"
    x-init="init()"
    class="relative"
    @keydown.escape.window="close()"
>
    {{-- Bell button --}}
    <button
        @click="toggle()"
        class="relative inline-flex items-center justify-center h-8 w-8 rounded-[7px]
               text-[#72809E] hover:text-[#394056] hover:bg-[#F1F3F5]
               transition-all duration-150"
        :aria-expanded="open.toString()"
        aria-label="Notifications"
        aria-haspopup="true"
    >
        <i class="bx bx-bell text-xl"></i>

        {{-- Unread badge --}}
        <span
            x-show="unreadCount > 0"
            x-text="unreadCount > 9 ? '9+' : unreadCount"
            style="display:none"
            class="absolute top-1 right-1 flex items-center justify-center
                   min-w-[15px] h-[15px] px-[3px] rounded-full
                   bg-[#e11d48] text-white text-[9px] font-bold leading-none"
        ></span>
    </button>

    {{-- Dropdown --}}
    <div
        x-show="open"
        @click.outside="close()"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
        style="display:none"
        class="absolute right-0 mt-2 w-80 max-w-[90vw] rounded-[14px]
               border border-[#e4e4e7] bg-white overflow-hidden z-50"
        style="box-shadow: 0 8px 40px rgba(0,0,0,0.14);"
    >
        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-[#F1F3F5]">
            <p class="text-[13px] font-bold text-[#09090b]">Notifications</p>
            <button
                x-show="unreadCount > 0"
                @click="markAllRead()"
                class="text-[11px] font-semibold text-[#06754E] hover:text-[#00965F] transition-colors"
            >
                Mark all read
            </button>
        </div>

        {{-- List --}}
        <div class="max-h-96 overflow-y-auto no-scrollbar divide-y divide-[#F4F4F5]">

            {{-- Loading --}}
            <template x-if="loading">
                <div class="px-4 py-8 text-center">
                    <p class="text-[12px] text-[#a1a1aa]">Loading…</p>
                </div>
            </template>

            {{-- Error --}}
            <template x-if="!loading && fetchError">
                <div class="px-4 py-8 text-center">
                    <p class="text-[12px] text-[#a1a1aa]">Could not load notifications.</p>
                </div>
            </template>

            {{-- Empty --}}
            <template x-if="!loading && !fetchError && notifications.length === 0">
                <div class="px-4 py-10 text-center">
                    <i class="bx bx-bell-off text-2xl text-[#d4d4d8]"></i>
                    <p class="text-[12px] text-[#a1a1aa] mt-2">No notifications yet</p>
                </div>
            </template>

            {{-- Items --}}
            <template x-if="!loading && !fetchError && notifications.length > 0">
                <div>
                    <template x-for="n in notifications" :key="n.id">
                        <button
                            @click="markRead(n)"
                            class="w-full text-left px-4 py-3 flex gap-3 items-start
                                   hover:bg-[#fafafa] transition-colors"
                            :class="n.read_at === null ? 'bg-[#FAFDFB]' : ''"
                        >
                            <span
                                class="mt-1 w-2 h-2 rounded-full shrink-0 transition-colors"
                                :class="n.read_at === null ? 'bg-[#00C075]' : 'bg-transparent'"
                            ></span>
                            <div class="min-w-0 flex-1">
                                <p class="text-[12.5px] text-[#09090b] leading-snug" x-text="n.message"></p>
                                <p class="text-[11px] text-[#a1a1aa] mt-0.5" x-text="n.time_ago"></p>
                            </div>
                        </button>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
function notificationBell(initialCount) {
    return {
        open:         false,
        loading:      false,
        fetchError:   false,
        notifications: [],
        unreadCount:  initialCount,

        init() {
            // nothing to prefetch — load on first open
        },

        toggle() {
            this.open ? this.close() : this.openPanel();
        },

        openPanel() {
            this.open = true;
            this.load();
        },

        close() {
            this.open = false;
        },

        async load() {
            this.loading    = true;
            this.fetchError = false;
            try {
                const res  = await fetch('/notifications/data', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                this.notifications = data.notifications;
                this.unreadCount   = data.unread_count;
            } catch {
                this.fetchError = true;
            } finally {
                this.loading = false;
            }
        },

        async markRead(n) {
            if (n.read_at !== null) return;  // already read
            try {
                await fetch(`/notifications/${n.id}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN':      document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With':  'XMLHttpRequest',
                    },
                });
                n.read_at = new Date().toISOString();
                this.unreadCount = Math.max(0, this.unreadCount - 1);
            } catch { /* silent */ }
        },

        async markAllRead() {
            try {
                await fetch('/notifications/read-all', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN':      document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With':  'XMLHttpRequest',
                    },
                });
                this.notifications.forEach(n => n.read_at = new Date().toISOString());
                this.unreadCount = 0;
            } catch { /* silent */ }
        },
    };
}
</script>
