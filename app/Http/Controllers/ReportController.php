<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display registration reports with filters
     */
    public function index(Request $request)
    {
        $query = Registration::with(['patient', 'doctor', 'polyclinic', 'bed.ward']);

        // Apply filters
        if ($request->filled('start_date')) {
            $query->whereDate('registration_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('registration_date', '<=', $request->end_date);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Order by latest first
        $registrations = $query->orderBy('registration_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Calculate statistics
        $stats = [
            'total' => $query->count(),
            'outpatient' => (clone $query)->where('type', 'outpatient')->count(),
            'inpatient' => (clone $query)->where('type', 'inpatient')->count(),
            'active' => (clone $query)->where('status', 'active')->count(),
            'completed' => (clone $query)->where('status', 'completed')->count(),
        ];

        return view('reports.index', compact('registrations', 'stats'));
    }

    /**
     * Export registration report to PDF
     */
    public function exportPdf(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'type' => 'nullable|in:outpatient,inpatient',
            'status' => 'nullable|in:active,completed,cancelled',
            'payment_method' => 'nullable|in:cash,insurance,bpjs',
        ]);

        $query = Registration::with(['patient', 'doctor', 'polyclinic', 'bed.ward']);

        // Apply same filters as index
        if ($request->filled('start_date')) {
            $query->whereDate('registration_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('registration_date', '<=', $request->end_date);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $registrations = $query->orderBy('registration_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate statistics
        $stats = [
            'total' => $registrations->count(),
            'outpatient' => $registrations->where('type', 'outpatient')->count(),
            'inpatient' => $registrations->where('type', 'inpatient')->count(),
            'active' => $registrations->where('status', 'active')->count(),
            'completed' => $registrations->where('status', 'completed')->count(),
            'cash' => $registrations->where('payment_method', 'cash')->count(),
            'insurance' => $registrations->where('payment_method', 'insurance')->count(),
            'bpjs' => $registrations->where('payment_method', 'bpjs')->count(),
        ];

        // Prepare filter info for PDF
        $filters = [
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'type' => $request->type,
            'status' => $request->status,
            'payment_method' => $request->payment_method,
        ];

        $pdf = Pdf::loadView('reports.pdf', compact('registrations', 'stats', 'filters'));
        $pdf->setPaper('a4', 'landscape');

        $filename = 'registration-report-' . now()->format('Y-m-d-His') . '.pdf';
        
        return $pdf->download($filename);
    }
}
