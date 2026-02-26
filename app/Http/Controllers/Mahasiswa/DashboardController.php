<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display mahasiswa dashboard
     */
    public function index()
    {
        return view('mahasiswa.dashboard');
    }
}
