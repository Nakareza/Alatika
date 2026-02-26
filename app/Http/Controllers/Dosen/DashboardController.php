<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display Dosen dashboard
     */
    public function index()
    {
        return view('dosen.dashboard');
    }
}
