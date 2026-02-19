<?php

namespace App\Livewire\AccountApproval;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class ManageQueue extends Component
{
    use WithPagination;

    public string $search = '';
    public string $role = 'all';
    public string $status = 'all';
    public string $sort = 'name_asc';

    protected string $paginationTheme = 'tailwind';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedRole(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        [$sortField, $sortDirection] = match ($this->sort) {
            'name_desc' => ['name', 'desc'],
            'newest' => ['id', 'desc'],
            'oldest' => ['id', 'asc'],
            default => ['name', 'asc'],
        };

        $users = User::query()
            ->with('roles')
            ->when(trim($this->search) !== '', function ($query) {
                $term = '%' . trim($this->search) . '%';
                $query->where(function ($subQuery) use ($term) {
                    $subQuery->where('name', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('phone_number', 'like', $term)
                        ->orWhere('office', 'like', $term);
                });
            })
            ->when($this->role !== 'all', function ($query) {
                $query->whereHas('roles', function ($roleQuery) {
                    $roleQuery->where('name', $this->role);
                });
            })
            ->when($this->status !== 'all', function ($query) {
                $query->where('account_status', $this->status);
            })
            ->orderBy($sortField, $sortDirection)
            ->paginate(10);

        return view('livewire.account-approval.manage-queue', [
            'users' => $users,
        ]);
    }
}
