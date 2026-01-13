<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOutpatientRegistrationRequest;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Polyclinic;
use App\Models\Registration;
use App\Services\QueueService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class OutpatientRegistrationController extends Controller
{
    protected $queueService;

    public function __construct(QueueService $queueService)
    {
        $this->queueService = $queueService;
    }

    /**
     * Display a listing of outpatient registrations
     */
    public function index(Request $request)
    {
        $query = Registration::with(['patient', 'doctor', 'polyclinic'])
            ->where('type', 'outpatient')
            ->latest('registration_date');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('registration_number', 'like', "%{$search}%")
                    ->orWhere('queue_number', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('medical_record_number', 'like', "%{$search}%");
                    });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date filter
        if ($request->filled('date')) {
            $query->whereDate('registration_date', $request->date);
        }

        $registrations = $query->paginate(15)->withQueryString();

        return view('outpatient.index', compact('registrations'));
    }

    /**
     * Show the form for creating a new outpatient registration
     */
    public function create()
    {
        $polyclinics = Polyclinic::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('outpatient.create', compact('polyclinics'));
    }

    /**
     * Store a newly created outpatient registration
     */
    public function store(StoreOutpatientRegistrationRequest $request)
    {
        try {
            $registration = DB::transaction(function () use ($request) {
                // Get polyclinic code for queue number generation
                $polyclinic = Polyclinic::findOrFail($request->polyclinic_id);
                
                // Generate queue number
                $queueNumber = $this->queueService->generateQueueNumber($polyclinic->code);
                
                // Generate registration number
                $registrationNumber = $this->generateRegistrationNumber();

                // Create registration
                $registration = Registration::create([
                    'registration_number' => $registrationNumber,
                    'type' => 'outpatient',
                    'registration_date' => now(),
                    'patient_id' => $request->patient_id,
                    'doctor_id' => $request->doctor_id,
                    'polyclinic_id' => $request->polyclinic_id,
                    'queue_number' => $queueNumber,
                    'complaint' => $request->complaint,
                    'payment_method' => $request->payment_method,
                    'status' => 'waiting',
                    'user_id' => auth()->id(),
                    'registration_time' => now()->format('H:i:s'),
                ]);

                return $registration;
            });

            return redirect()
                ->route('outpatient.show', $registration)
                ->with('success', 'Outpatient registration created successfully. Queue number: ' . $registration->queue_number);
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create registration: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified outpatient registration
     */
    public function show(Registration $registration)
    {
        $registration->load(['patient', 'doctor', 'polyclinic']);

        return view('outpatient.show', compact('registration'));
    }

    /**
     * Print registration receipt as PDF
     */
    public function print(Registration $registration)
    {
        $registration->load(['patient', 'doctor', 'polyclinic']);

        // Generate QR code for registration
        $qrData = json_encode([
            'type' => 'outpatient',
            'reg_no' => $registration->registration_number,
            'queue' => $registration->queue_number,
            'patient' => $registration->patient->medical_record_number,
            'date' => $registration->registration_date->format('Y-m-d H:i'),
        ]);

        $qrCode = QrCode::size(100)->margin(1)->generate($qrData);

        $pdf = Pdf::loadView('outpatient.receipt', compact('registration', 'qrCode'));
        
        return $pdf->download('receipt-' . $registration->registration_number . '.pdf');
    }

    /**
     * Get available doctors for a polyclinic (AJAX)
     */
    public function getDoctors(Request $request)
    {
        $polyclinicId = $request->polyclinic_id;

        // Get doctors for selected polyclinic
        $doctors = Doctor::where('polyclinic_id', $polyclinicId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'specialization']);

        return response()->json($doctors);
    }

    /**
     * Generate unique registration number
     * Format: REG-YYYYMMDD-XXXX
     */
    protected function generateRegistrationNumber(): string
    {
        $today = now()->format('Ymd');
        $prefix = "REG-{$today}";

        $lastRegistration = Registration::where('registration_number', 'like', "{$prefix}-%")
            ->lockForUpdate()
            ->orderBy('registration_number', 'desc')
            ->first();

        if ($lastRegistration) {
            $parts = explode('-', $lastRegistration->registration_number);
            $lastSequence = (int) end($parts);
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }

        $sequence = str_pad($newSequence, 4, '0', STR_PAD_LEFT);

        return "{$prefix}-{$sequence}";
    }
}
