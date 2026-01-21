<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AcademicStructureController extends Controller
{
    public function index()
    {
        return view('AcademicStructure.index');
    }
}
