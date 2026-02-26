<?php

namespace App\Http\Controllers\Kalab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display KA Lab dashboard
     */
    public function index()
    {
        return view('kalab.dashboard');
    }
}
