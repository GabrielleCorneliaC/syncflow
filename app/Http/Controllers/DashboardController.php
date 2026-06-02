<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Data statistik untuk chart (nanti bisa diganti query nyata dari tiap modul)
        $stats = [
            'total_users'    => User::count(),
            'new_this_month' => User::whereMonth('created_at', now()->month)->count(),
        ];

        return view('dashboard.index', compact('stats'));
    }
}
