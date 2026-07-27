<?php

namespace App\Http\Controllers;

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

        return view('dashboard.index', compact('user', 'data'));
    }
}
