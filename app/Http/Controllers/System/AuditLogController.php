<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    // All filtering, querying, and pagination is handled by the
    // App\Livewire\AuditLog Livewire component.
    // This controller only loads the page view and applies route middleware.
    public function index(Request $request)
    {
        return view('System.AuditLog.index');
    }
}
