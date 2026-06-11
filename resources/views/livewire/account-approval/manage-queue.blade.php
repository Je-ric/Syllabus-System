<div
    x-data="{
        selected: [],
        selectedStatus: null,
        executing: false,
        statusMap: @js($users->pluck('account_status', 'id')->toArray()),
        conflictToast: false,
        conflictTimer: null,
        conflictMsg: '',

        init() {
            {{-- Re-sync statusMap whenever Livewire re-renders (fixes stale state after bulk) --}}
            $wire.$on('bulk-done', () => {
                this.selected      = [];
                this.selectedStatus = null;
                this.executing     = false;
                this.bulkModal     = false;
            });

            Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
                succeed(({ snapshot, effect }) => {
                    this.$nextTick(() => {
                        const newMap = @js($users->pluck('account_status', 'id')->toArray());
                        {{-- newMap above is evaluated once at render; we need runtime values --}}
                        {{-- Instead read from the re-rendered DOM's data attribute --}}
                        const el = this.$el.querySelector('[data-status-map]');
                        if (el) {
                            try { this.statusMap = JSON.parse(el.dataset.statusMap); } catch(e) {}
                        }
                    });
                });
            });
        },

        get pageIds() { return Object.keys(this.statusMap); },

        get selectAll() {
            return this.pageIds.length > 0 && this.pageIds.every(id => this.selected.includes(id));
        },
        set selectAll(v) {
            if (!v) { this.selected = []; this.selectedStatus = null; return; }
            const statuses = [...new Set(this.pageIds.map(id => this.statusMap[id]))];
            if (statuses.length > 1) { this.showConflict('All users on this page must share the same status to select all.'); return; }
            this.selected      = [...this.pageIds];
            this.selectedStatus = statuses[0];
        },

        toggleRow(id) {
            const rowStatus = this.statusMap[id];
            const i = this.selected.indexOf(id);
            if (i !== -1) {
                this.selected.splice(i, 1);
                if (this.selected.length === 0) this.selectedStatus = null;
                return;
            }
            if (this.selectedStatus && rowStatus !== this.selectedStatus) {
                this.showConflict('Only same-status users can be bulk-selected.');
                return;
            }
            this.selected.push(id);
            this.selectedStatus = rowStatus;
        },

        isSelected(id)  { return this.selected.includes(id); },
        canSelect(id)   { return !this.selectedStatus || this.statusMap[id] === this.selectedStatus; },

        showConflict(msg) {
            this.conflictMsg   = msg;
            this.conflictToast = true;
            clearTimeout(this.conflictTimer);
            this.conflictTimer = setTimeout(() => { this.conflictToast = false; }, 3500);
        },

        get bulkActions() {
            const map = {
                pending:  [{ key: 'approve', label: 'Approve',  icon: 'bx-check',    variant: 'confirm' },
                           { key: 'reject',  label: 'Reject',   icon: 'bx-x',        variant: 'danger'  }],
                active:   [{ key: 'disable', label: 'Disable',  icon: 'bx-pause',    variant: 'disable' }],
                disabled: [{ key: 'approve', label: 'Activate', icon: 'bx-check',    variant: 'confirm' }],
                rejected: [{ key: 'restore', label: 'Restore',  icon: 'bx-revision', variant: 'restore' }],
            };
            return this.selectedStatus ? (map[this.selectedStatus] || []) : [];
        },

        bulkModal: false,
        bulkAction: '',
        openBulk(action) { if (!this.selected.length) return; this.bulkAction = action; this.bulkModal = true; },

        syncAndExecute() {
            this.executing = true;
            $wire.executeBulk([...this.selected], this.bulkAction)
                .catch(() => { this.executing = false; });
        },
    }"
    class="space-y-4">

    {{-- Live status map — updated on every Livewire re-render --}}
    <span class="hidden" data-status-map='@json($users->pluck('account_status', 'id')->toArray())'></span>

    @include('livewire.account-approval.partials.filters')
    @include('livewire.account-approval.partials.bulk-bar')
    @include('livewire.account-approval.partials.bulk-modal')
    @include('livewire.account-approval.partials.table')

</div>
