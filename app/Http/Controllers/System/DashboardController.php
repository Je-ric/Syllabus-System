<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\DashboardService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboard) {}

    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $data = $this->dashboard->getDashboardData($user);

        return view('System.Dashboard.index', compact('user', 'data'));
    }
}
