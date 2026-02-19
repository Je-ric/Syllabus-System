<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return view('Authentication.viewDetails', compact('user'));
    }

    public function update()
    {
        
    }

    public function changePassword()
    {

    }
}
