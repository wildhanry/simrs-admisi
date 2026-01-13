<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInpatientRegistrationRequest;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Ward;
use App\Models\Bed;
use App\Models\Registration;
use App\Services\BedAllocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Exception;

class InpatientRegistrationController extends Controller
{
    protected $bedAllocationService;

    public function __construct(BedAllocationService $bedAllocationService)
    {
        $this->bedAllocationService = $bedAllocationService;
    }

    /**
     * Display a listing of inpatient registrations
     */
    public function index(Request $request)
    {
        $query = Registration::with(['patient', 'doctor', 'ward', 'bed'])
            ->where('type', 'inpatient')
            ->latest('registration_date');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('registration_number', 'like', "%{$search}%")
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

        return view('inpatient.index', compact('registrations'));
    }

    /**
     * Show the form for creating a new inpatient registration
     */
    public function create()
    {
        // Get available beds grouped by ward
        $availableBeds = $this->bedAllocationService->getAvailableBeds();
        
        // Get wards with available beds
        $wards = Ward::whereHas('beds', function ($query) {
            $query->where('status', 'available');
        })->with(['beds' => function ($query) {
            $query->where('status', 'available')->orderBy('bed_number');
        }])->orderBy('name')->get();

        // Get active doctors
        $doctors = Doctor::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('inpatient.create', compact('wards', 'doctors', 'availableBeds'));
    }

    /**
     * Store a newly created inpatient registration
     */
    public function store(StoreInpatientRegistrationRequest $request)
    {
        try {
            $registration = DB::transaction(function () use ($request) {
                // Step 1: Validate bed availability before locking
                $this->bedAllocationService->validateBedAvailability($request->bed_id);
                
                // Step 2: Allocate bed with pessimistic lock (SELECT FOR UPDATE)
                // This prevents double booking even with concurrent requests
                $bed = $this->bedAllocationService->allocateBed($request->bed_id);
                
                // Step 3: Generate registration number
                $registrationNumber = $this->generateRegistrationNumber();
                
                // Step 4: Create registration record
                $registration = Registration::create([
                    'registration_number' => $registrationNumber,
                    'type' => 'inpatient',
                    'registration_date' => now(),
                    'patient_id' => $request->patient_id,
                    'doctor_id' => $request->doctor_id,
                    'ward_id' => $bed->ward_id,
                    'bed_id' => $bed->id,
                    'diagnosis' => $request->diagnosis,
                    'planned_admission_date' => $request->planned_admission_date ?? now(),
                    'estimated_discharge_date' => $request->estimated_discharge_date,
                    'payment_method' => $request->payment_method,
                    'status' => 'waiting',
                    'user_id' => auth()->id(),
                    'registration_time' => now()->format('H:i:s'),
                    'notes' => $request->notes,
                ]);

                return $registration;
            });

            return redirect()
                ->route('inpatient.show', $registration)
                ->with('success', 'Inpatient registration created successfully. Bed ' . $registration->bed->bed_number . ' has been allocated.');
                
        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create registration: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified inpatient registration
     */
    public function show(Registration $registration)
    {
        $registration->load(['patient', 'doctor', 'ward', 'bed']);

        return view('inpatient.show', compact('registration'));
    }

    /**
     * Discharge patient and release bed
     */
    public function discharge(Registration $registration)
    {
        try {
            DB::transaction(function () use ($registration) {
                // Release the bed
                if ($registration->bed_id) {
                    $this->bedAllocationService->releaseBed($registration->bed_id);
                }

                // Update registration status
                $registration->update([
                    'status' => 'completed',
                    'actual_discharge_date' => now(),
                ]);
            });

            return redirect()
                ->route('inpatient.show', $registration)
                ->with('success', 'Patient discharged successfully. Bed has been released.');
                
        } catch (Exception $e) {
            return back()
                ->with('error', 'Failed to discharge patient: ' . $e->getMessage());
        }
    }

    /**
     * Print admission letter as PDF
     */
    public function print(Registration $registration)
    {
        $registration->load(['patient', 'doctor', 'ward', 'bed']);

        // Generate QR code with admission data
        $qrData = json_encode([
            'type' => 'inpatient_admission',
            'registration_number' => $registration->registration_number,
            'patient_mr' => $registration->patient->medical_record_number,
            'bed' => $registration->bed->ward->name . ' - ' . $registration->bed->bed_number,
            'admission_date' => $registration->admission_date,
            'timestamp' => now()->toIso8601String()
        ]);
        
        $qrCode = QrCode::size(100)->margin(1)->generate($qrData);
        
        $pdf = Pdf::loadView('inpatient.admission-letter', compact('registration', 'qrCode'));
        
        return $pdf->download('admission-letter-' . $registration->registration_number . '.pdf');
    }

    /**
     * Get available beds for a ward (AJAX)
     */
    public function getBeds(Request $request)
    {
        $wardId = $request->ward_id;

        $beds = Bed::where('ward_id', $wardId)
            ->where('status', 'available')
            ->orderBy('bed_number')
            ->get(['id', 'bed_number', 'bed_type']);

        return response()->json($beds);
    }

    /**
     * Check bed availability (AJAX)
     */
    public function checkBedAvailability(Request $request)
    {
        $bedId = $request->bed_id;
        
        $available = $this->bedAllocationService->isBedAvailable($bedId);
        
        if (!$available) {
            $bed = Bed::find($bedId);
            return response()->json([
                'available' => false,
                'message' => "Bed {$bed->bed_number} is no longer available. Current status: {$bed->status}",
            ]);
        }

        return response()->json([
            'available' => true,
            'message' => 'Bed is available',
        ]);
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
