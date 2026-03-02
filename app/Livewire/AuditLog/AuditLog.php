<?php

namespace App\Livewire\AuditLog;

use App\Models\AuditLog as AuditLogModel;
use App\Models\User;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * AuditLog — Livewire component for the Audit Logs page.
 *
 * Real-time strategy: wire:poll.5s calls refresh() every 5 seconds
 * while $liveRefresh is true. No WebSockets or Laravel Echo required —
 * polling is perfectly appropriate for audit logs where a few seconds
 * of delay is acceptable and the query is cheap.
 *
 * Filters are Livewire public properties bound with wire:model.live,
 * so any filter change immediately re-runs the query without a page reload.
 * They are also synced to the URL via #[Url] so filters survive refresh.
 */
class AuditLog extends Component
{
    use WithPagination;

    // ── Filters (synced to URL query string) ──────────────────────────────
    #[Url(as: 'user_id',      except: '')]  public string $userId      = '';
    #[Url(as: 'module',       except: '')]  public string $module      = '';
    #[Url(as: 'action',       except: '')]  public string $action      = '';
    #[Url(as: 'reference_id', except: '')]  public string $referenceId = '';
    #[Url(as: 'date_from',    except: '')]  public string $dateFrom    = '';
    #[Url(as: 'date_to',      except: '')]  public string $dateTo      = '';
    #[Url(as: 'q',            except: '')]  public string $keyword     = '';

    // ── UI state ─────────────────────────────────────────────────────────
    public bool   $liveRefresh    = true;
    public string $lastRefreshed  = '';

    // ──────────────────────────────────────────────────────────────────────

    public function mount(): void
    {
        $this->lastRefreshed = now()->format('H:i:s');
    }

    /** Reset to page 1 whenever any filter value changes. */
    public function updatedUserId():      void { $this->resetPage(); }
    public function updatedModule():      void { $this->resetPage(); }
    public function updatedAction():      void { $this->resetPage(); }
    public function updatedReferenceId(): void { $this->resetPage(); }
    public function updatedDateFrom():    void { $this->resetPage(); }
    public function updatedDateTo():      void { $this->resetPage(); }
    public function updatedKeyword():     void { $this->resetPage(); }

    /** Clear all filters and reset pagination. */
    public function clearFilters(): void
    {
        $this->reset(['userId', 'module', 'action', 'referenceId', 'dateFrom', 'dateTo', 'keyword']);
        $this->resetPage();
    }

    /**
     * Called by wire:poll every 5 seconds.
     * Just updates the "last refreshed" timestamp — Livewire re-renders
     * the component automatically on each poll, which re-runs render().
     */
    public function refresh(): void
    {
        $this->lastRefreshed = now()->format('H:i:s');
    }

    public function render()
    {
        $query = AuditLogModel::query()->with('user');

        if ($this->userId !== '')      $query->where('user_id',     (int) $this->userId);
        if ($this->module !== '')      $query->where('module',       $this->module);
        if ($this->action !== '')      $query->where('action',       $this->action);
        if ($this->referenceId !== '') $query->where('reference_id', (int) $this->referenceId);
        if ($this->dateFrom !== '')    $query->whereDate('timestamp', '>=', $this->dateFrom);
        if ($this->dateTo !== '')      $query->whereDate('timestamp', '<=', $this->dateTo);

        if ($this->keyword !== '') {
            $kw = trim($this->keyword);
            $query->where(fn ($q) => $q
                ->where('description', 'like', "%{$kw}%")
                ->orWhere('module',    'like', "%{$kw}%")
                ->orWhere('action',    'like', "%{$kw}%")
            );
        }

        $logs = $query
            ->orderByDesc('timestamp')
            ->orderByDesc('id')
            ->paginate(20);

        $users   = User::orderBy('name')->get(['id', 'name']);
        $modules = AuditLogModel::select('module')->distinct()->orderBy('module')->pluck('module');
        $actions = AuditLogModel::select('action')->distinct()->orderBy('action')->pluck('action');

        return view('livewire.audit-log.audit-log', compact('logs', 'users', 'modules', 'actions'));
    }
}
