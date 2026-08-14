<?php

namespace App\Livewire\AccountApproval;

use App\Models\User;
use App\Services\Authentication\AccountApprovalService;
use Livewire\Component;
use Livewire\WithPagination;

class ManageQueue extends Component
{
    use WithPagination;

    public string $search = '';
    public string $role   = 'all';
    public string $status = 'all';
    public string $sort   = 'newest';

    protected string $paginationTheme = 'tailwind';

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'role', 'status', 'sort'])) {
            $this->resetPage();
            $this->dispatch('filter-changed');
        }
    }

    public function updatingPage(): void
    {
        $this->dispatch('page-changed');
    }

    /**
     * Called by Alpine syncAndExecute() after it sets selectedIds + bulkAction
     * via $wire.set(). Accepts them as direct parameters to avoid extra round-trips.
     */
    public function executeBulk(AccountApprovalService $service, array $ids, string $action): void
    {
        $allowed = ['approve', 'reject', 'disable', 'restore'];
        if (!in_array($action, $allowed) || empty($ids)) return;

        $intIds = array_map('intval', $ids);

        // Validate: all selected users must have the expected status for this action
        $validStatuses = match ($action) {
            'approve' => ['pending', 'disabled'],
            'reject'  => ['pending'],
            'disable' => ['active'],
            'restore' => ['rejected'],
        };

        $allValid = User::whereIn('id', $intIds)
            ->whereNotIn('account_status', $validStatuses)
            ->doesntExist();

        if (!$allValid) return;

        $total     = count($intIds);
        $succeeded = 0;
        $failed    = 0;

        foreach ($intIds as $id) {
            try {
                match ($action) {
                    'approve' => $service->approve($id),
                    'reject'  => $service->reject($id),
                    'disable' => $service->disable($id),
                    'restore' => $service->restore($id),
                };
                $succeeded++;
            } catch (\Throwable) {
                $failed++;
            }
        }

        // Don't reset page - let users stay on current page but refresh data
        // The component will automatically re-render with updated user statuses
        $this->dispatch('bulk-done');

        if ($failed === 0) {
            $label = ucfirst($action) . 'd';
            $this->dispatch('lw-toast', type: 'success', message: "{$label} {$succeeded} " . str('user')->plural($succeeded) . ' successfully.');
        } elseif ($succeeded === 0) {
            $this->dispatch('lw-toast', type: 'error', message: "Bulk {$action} failed for all {$total} " . str('user')->plural($total) . '.');
        } else {
            $this->dispatch('lw-toast', type: 'warning', message: "{$succeeded} of {$total} users processed. {$failed} failed.");
        }
    }

    public function resetBulkState(): void
    {
        $this->dispatch('bulk-state-reset');
    }

    private function buildQuery()
    {
        [$sortField, $sortDir] = match ($this->sort) {
            'name_asc'    => ['name', 'asc'],
            'name_desc'   => ['name', 'desc'],
            'status_asc'  => ['account_status', 'asc'],
            'status_desc' => ['account_status', 'desc'],
            'oldest'      => ['id', 'asc'],
            default       => ['id', 'desc'],
        };

        return User::query()
            ->select(['id', 'name', 'email', 'email_verified_at', 'phone_number', 'office', 'account_status', 'created_at'])
            ->with('roles:id,name')
            ->when(strlen(trim($this->search)) >= 2, function ($q) {
                $term = '%' . trim($this->search) . '%';
                $q->where(fn($s) =>
                    $s->where('name', 'like', $term)
                      ->orWhere('email', 'like', $term)
                      ->orWhere('phone_number', 'like', $term)
                      ->orWhere('office', 'like', $term)
                );
            })
            ->when($this->role !== 'all', fn($q) =>
                $q->whereHas('roles', fn($r) => $r->where('name', $this->role))
            )
            ->when($this->status !== 'all', fn($q) =>
                $q->where('account_status', $this->status)
            )
            ->orderBy($sortField, $sortDir);
    }

    public function render()
    {
        $users = $this->buildQuery()->paginate(10);
        return view('livewire.account-approval.manage-queue', compact('users'));
    }
}
