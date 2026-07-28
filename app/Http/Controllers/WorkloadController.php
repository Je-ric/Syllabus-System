<?php

namespace App\Http\Controllers;

use App\Models\CaisTeachingLoad;
use App\Services\CaisAPI\WorkloadSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkloadController extends Controller
{
    public function __construct(private readonly WorkloadSyncService $syncService) {}

    /**
     * Show the faculty's synced teaching loads from local DB.
     * Does NOT hit CAIS — displays whatever was last synced.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $loads = CaisTeachingLoad::with(['classSchedule.caisSemester', 'caisSemester'])
            ->where('user_id', $user->id)
            ->active()
            ->orderByDesc('synced_at')
            ->get();

        return view('Faculty.workload.index', compact('loads', 'user'));
    }

    /**
     * Verify CAIS credentials, fetch workloads, and sync into local DB.
     * Called when the faculty submits the credentials modal.
     */
    public function sync(Request $request)
    {
        $request->validate([
            'cais_employee_id' => ['required', 'string'],
            'cais_password'    => ['required', 'string'],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        try {
            $this->syncService->syncForUser(
                $user,
                $request->cais_employee_id,
                $request->cais_password
            );

            return redirect()->route('workload.index')
                ->with('toast', ['message' => 'Workload synced successfully.', 'type' => 'success']);

        } catch (\RuntimeException $e) {
            // Invalid credentials or CAIS unavailable — show on the same page
            return redirect()->route('workload.index')
                ->with('toast', ['message' => $e->getMessage(), 'type' => 'error'])
                ->with('open_sync_modal', true);

        } catch (\Throwable $e) {
            return redirect()->route('workload.index')
                ->with('toast', ['message' => 'Sync failed. Please try again later.', 'type' => 'error'])
                ->with('open_sync_modal', true);
        }
    }
}
