<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * All filtering, querying, and pagination is handled by the
     * App\Livewire\AuditLog Livewire component.
     *
     * This controller method only exists to load the page view and
     * apply any route middleware (auth, role checks, etc.).
     */
    public function index(Request $request)
    {
        return view('AuditLog.index');
    }
}
