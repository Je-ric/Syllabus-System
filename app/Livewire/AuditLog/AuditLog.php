<?php

namespace App\Livewire\AuditLog;

use App\Models\AuditLog as AuditLogModel;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AuditLog extends Component
{
    use WithPagination;

    // ── Filters ───────────────────────────────────────────────────────────
    #[Url(as: 'user',   except: '')] public string $userId      = '';
    #[Url(as: 'mod',    except: '')] public string $module      = '';
    #[Url(as: 'act',    except: '')] public string $action      = '';
    #[Url(as: 'ref',    except: '')] public string $referenceId = '';
    #[Url(as: 'from',   except: '')] public string $dateFrom    = '';
    #[Url(as: 'to',     except: '')] public string $dateTo      = '';
    #[Url(as: 'q',      except: '')] public string $keyword     = '';

    // ── UI state ──────────────────────────────────────────────────────────
    public bool   $liveRefresh   = true;
    public int    $pollInterval  = 30; // Default to 30s instead of 10s
    public string $lastRefreshed = '';
    public bool   $isPageVisible = true;
    public bool   $isLoading     = false;

    // ── Cached filter options (loaded once in mount) ──────────────────────
    public array $users   = [];
    public array $modules = [];
    public array $actions = [];

    // ─────────────────────────────────────────────────────────────────────

    public function mount(): void
    {
        $this->lastRefreshed = now()->format('H:i:s');

        // These rarely change — load once per page load, not on every filter update
        $this->users = User::orderBy('name')->get(['id', 'name'])->toArray();

        $this->modules = AuditLogModel::select('module')
            ->distinct()->orderBy('module')->pluck('module')->toArray();

        $this->actions = AuditLogModel::select('action')
            ->distinct()->orderBy('action')->pluck('action')->toArray();
    }

    // Reset page on any filter change
    public function updated(string $property): void
    {
        if (in_array($property, ['userId', 'module', 'action', 'referenceId', 'dateFrom', 'dateTo', 'keyword'])) {
            $this->resetPage();
        }
    }

    public function clearFilters(): void
    {
        $this->reset(['userId', 'module', 'action', 'referenceId', 'dateFrom', 'dateTo', 'keyword']);
        $this->resetPage();
    }

    public function refresh(): void
    {
        $this->isLoading = true;
        $this->lastRefreshed = now()->format('H:i:s');
        $this->isLoading = false;
    }

    #[Computed]
    public function logs()
    {
        $query = AuditLogModel::query()
            ->with('user:id,name')   // only fetch what we display
            ->select(['id', 'user_id', 'action', 'module', 'reference_id', 'description', 'timestamp']);

        if ($this->userId !== '')      $query->where('user_id',      (int) $this->userId);
        if ($this->module !== '')      $query->where('module',        $this->module);
        if ($this->action !== '')      $query->where('action',        $this->action);
        if ($this->referenceId !== '') $query->where('reference_id',  (int) $this->referenceId);
        if ($this->dateFrom !== '')    $query->whereDate('timestamp', '>=', $this->dateFrom);
        if ($this->dateTo !== '')      $query->whereDate('timestamp', '<=', $this->dateTo);

        if ($this->keyword !== '') {
            $kw = '%' . trim($this->keyword) . '%';
            $query->where(fn ($q) => $q
                ->where('description', 'like', $kw)
                ->orWhere('module',    'like', $kw)
                ->orWhere('action',    'like', $kw)
            );
        }

        return $query
            ->orderByDesc('id')   // id is clustered PK — faster than timestamp for ordering
            ->paginate(20);
    }

    public function render()
    {
        return view('livewire.audit-log.audit-log');
    }
}
