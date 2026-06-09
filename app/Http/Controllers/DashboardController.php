<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /**
         * Data statistik untuk dashboard.
         * Nanti bisa diganti query nyata dari masing-masing modul.
         * Untuk saat ini pakai data dummy yang realistis.
         */
        $stats = [
            // Progres tugas (untuk progress bar & angka bulat)
            'tasks_done'     => 12,
            'tasks_total'    => 18,
            'progress_pct'   => round((12 / 18) * 100), // = 67

            // Jumlah user (untuk admin)
            'total_users'    => User::count(),
            'new_this_month' => User::whereMonth('created_at', now()->month)->count(),
        ];

        return view('dashboard.index', compact('stats'));
    }
}
