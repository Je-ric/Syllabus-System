<div
    x-data="{
        selected: [],
        selectedStatus: null,
        executing: false,
        statusMap: @js($users->pluck('account_status', 'id')->toArray()),
        conflictToast: false,
        conflictTimer: null,
        conflictMsg: '',
        expandedRows: {},

        init() {
            // Re-sync statusMap whenever Livewire re-renders
            Livewire.hook('message.processed', ({ component, message }) => {
                this.$nextTick(() => {
                    const el = this.$el.querySelector('[data-status-map]');
                    if (el) {
                        try { 
                            this.statusMap = JSON.parse(el.dataset.statusMap); 
                            // Recalculate selectedStatus based on updated statusMap
                            this.recalculateSelectedStatus();
                        } catch(e) {}
                    }
                });
            });

            // Handle bulk operation completion - only clear selection data (server-confirmed state)
            $wire.$on('bulk-done', () => {
                this.$nextTick(() => {
                    this.selected      = [];
                    this.selectedStatus = null;
                    // Don't touch executing/bulkModal here - let the promise chain handle UI state
                });
            });

            // Handle page changes
            $wire.$on('page-changed', () => {
                this.$nextTick(() => {
                    this.selected      = [];
                    this.selectedStatus = null;
                    this.executing     = false;
                    this.bulkModal     = false;
                    this.expandedRows = {};
                });
            });

            // Handle filter changes
            $wire.$on('filter-changed', () => {
                this.$nextTick(() => {
                    this.selected      = [];
                    this.selectedStatus = null;
                    this.executing     = false;
                    this.bulkModal     = false;
                    this.expandedRows = {};
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
            this.selected = [...this.pageIds];
            this.selectedStatus = statuses[0]; // All users have the same status
        },

        toggleRow(id) {
            const rowStatus = this.statusMap[id];
            const i = this.selected.indexOf(id);
            if (i !== -1) {
                this.selected.splice(i, 1);
                // Always recalculate selectedStatus after removal
                if (this.selected.length === 0) {
                    this.selectedStatus = null;
                } else {
                    const remainingStatuses = this.selected.map(id => this.statusMap[id]);
                    const uniqueStatuses = [...new Set(remainingStatuses)];
                    this.selectedStatus = uniqueStatuses.length === 1 ? uniqueStatuses[0] : null;
                }
                return;
            }
            // Check for status conflict before adding
            if (this.selected.length > 0) {
                const existingStatuses = this.selected.map(id => this.statusMap[id]);
                const uniqueExisting = [...new Set(existingStatuses)];
                if (uniqueExisting.length === 1 && rowStatus !== uniqueExisting[0]) {
                    this.showConflict('Only same-status users can be bulk-selected.');
                    return;
                }
            }
            this.selected.push(id);
            // Always recalculate selectedStatus based on current statusMap
            this.recalculateSelectedStatus();
        },

        recalculateSelectedStatus() {
            if (this.selected.length === 0) {
                this.selectedStatus = null;
                return;
            }
            const allSelectedStatuses = this.selected.map(id => this.statusMap[id]);
            const uniqueStatuses = [...new Set(allSelectedStatuses)];
            this.selectedStatus = uniqueStatuses.length === 1 ? uniqueStatuses[0] : null;
        },

        isSelected(id)  { return this.selected.includes(id); },
        canSelect(id)   { return !this.selectedStatus || this.statusMap[id] === this.selectedStatus; },

        showConflict(msg) {
            this.conflictMsg   = msg;
            this.conflictToast = true;
            clearTimeout(this.conflictTimer);
            this.conflictTimer = setTimeout(() => { this.conflictToast = false; }, 3500);
        },

        toggleExpand(id) {
            this.expandedRows[id] = !this.expandedRows[id];
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
        openBulk(action) { 
            if (!this.selected.length) return; 
            this.bulkAction = action; 
            this.$nextTick(() => {
                this.bulkModal = true;
            });
        },

        syncAndExecute() {
            this.executing = true;
            $wire.executeBulk([...this.selected], this.bulkAction)
                .then(() => {
                    // Reload page on success to reset all state
                    window.location.reload();
                })
                .catch(() => { 
                    this.$nextTick(() => {
                        this.executing = false;
                    });
                });
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
