<?php

namespace App\Livewire\AccountApproval;

use App\Models\User;
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
        }
    }

    public function render()
    {
        [$sortField, $sortDir] = match ($this->sort) {
            'name_asc'    => ['name', 'asc'],
            'name_desc'   => ['name', 'desc'],
            'email_asc'   => ['email', 'asc'],
            'email_desc'  => ['email', 'desc'],
            'status_asc'  => ['account_status', 'asc'],
            'status_desc' => ['account_status', 'desc'],
            'oldest'      => ['id', 'asc'],
            default       => ['id', 'desc'],
        };

        $users = User::query()
            ->select(['id', 'name', 'email', 'phone_number', 'office', 'account_status', 'created_at'])
            ->with('roles:id,name')
            ->when(trim($this->search) !== '', function ($q) {
                $term = '%' . trim($this->search) . '%';
                $q->where(fn ($s) =>
                    $s->where('name', 'like', $term)
                      ->orWhere('email', 'like', $term)
                      ->orWhere('phone_number', 'like', $term)
                      ->orWhere('office', 'like', $term)
                );
            })
            ->when($this->role !== 'all', fn ($q) =>
                $q->whereHas('roles', fn ($r) => $r->where('name', $this->role))
            )
            ->when($this->status !== 'all', fn ($q) =>
                $q->where('account_status', $this->status)
            )
            ->orderBy($sortField, $sortDir)
            ->paginate(10);

        return view('livewire.account-approval.manage-queue', compact('users'));
    }
}
