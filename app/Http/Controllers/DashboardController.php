<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard
     */
    public function index()
    {
        $user = auth()->user();

        // Get statistics
        $stats = [
            'total_patients' => \App\Models\Patient::count(),
            'today_registrations' => \App\Models\Registration::today()->count(),
            'available_beds' => \App\Models\Bed::where('status', 'available')->count(),
            'active_doctors' => \App\Models\Doctor::active()->count(),
        ];

        return view('dashboard', compact('user', 'stats'));
    }
}
