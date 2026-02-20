<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::query()->with('user');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }

        if ($request->filled('module')) {
            $query->where('module', $request->string('module')->toString());
        }

        if ($request->filled('action')) {
            $query->where('action', $request->string('action')->toString());
        }

        if ($request->filled('reference_id')) {
            $query->where('reference_id', $request->integer('reference_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('timestamp', '>=', $request->string('date_from')->toString());
        }

        if ($request->filled('date_to')) {
            $query->whereDate('timestamp', '<=', $request->string('date_to')->toString());
        }

        if ($request->filled('q')) {
            $keyword = trim($request->string('q')->toString());
            $query->where(function ($subQuery) use ($keyword) {
                $subQuery->where('description', 'like', "%{$keyword}%")
                    ->orWhere('module', 'like', "%{$keyword}%")
                    ->orWhere('action', 'like', "%{$keyword}%");
            });
        }

        $logs = $query
            ->orderByDesc('timestamp')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $users = User::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $modules = AuditLog::query()
            ->select('module')
            ->distinct()
            ->orderBy('module')
            ->pluck('module');

        $actions = AuditLog::query()
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('AuditLog.index', compact('logs', 'users', 'modules', 'actions'));
    }
}
