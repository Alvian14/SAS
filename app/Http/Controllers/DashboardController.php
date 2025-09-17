<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $serverTime = now()->format('H:i:s');
        return view('pages.dashboard.dashboard', compact('serverTime'));
    }
}
