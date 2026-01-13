<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Services\MedicalRecordService;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PatientController extends Controller
{
    protected $medicalRecordService;

    public function __construct(MedicalRecordService $medicalRecordService)
    {
        $this->medicalRecordService = $medicalRecordService;
    }

    /**
     * Display a listing of patients
     */
    public function index(Request $request)
    {
        $query = Patient::query();

        // Regular search (non-AJAX)
        if ($search = $request->input('search')) {
            $query->search($search);
        }

        $patients = $query->latest()->paginate(15)->withQueryString();

        return view('patients.index', compact('patients'));
    }

    /**
     * AJAX search for patients
     */
    public function search(Request $request)
    {
        $search = $request->input('q');
        
        $patients = Patient::search($search)
            ->limit(10)
            ->get(['id', 'medical_record_number', 'nik', 'name', 'birth_date', 'gender', 'phone']);

        return response()->json($patients);
    }

    /**
     * Show the form for creating a new patient
     */
    public function create()
    {
        // Generate medical record number for display
        $mrn = $this->medicalRecordService->generateMedicalRecordNumber();
        
        return view('patients.create', compact('mrn'));
    }

    /**
     * Store a newly created patient
     */
    public function store(StorePatientRequest $request)
    {
        // Generate medical record number if not provided
        $data = $request->validated();
        
        if (empty($data['medical_record_number'])) {
            $data['medical_record_number'] = $this->medicalRecordService->generateMedicalRecordNumber();
        }

        $patient = Patient::create($data);

        return redirect()->route('patients.index')
            ->with('success', "Patient created successfully. MR: {$patient->medical_record_number}");
    }

    /**
     * Display the specified patient
     */
    public function show(Patient $patient)
    {
        $patient->load(['registrations' => function ($query) {
            $query->latest()->take(10);
        }]);

        return view('patients.show', compact('patient'));
    }

    /**
     * Show the form for editing the patient
     */
    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    /**
     * Update the specified patient
     */
    public function update(UpdatePatientRequest $request, Patient $patient)
    {
        $patient->update($request->validated());

        return redirect()->route('patients.index')
            ->with('success', 'Patient updated successfully.');
    }

    /**
     * Remove the specified patient
     */
    public function destroy(Patient $patient)
    {
        $patient->delete();

        return redirect()->route('patients.index')
            ->with('success', 'Patient deleted successfully.');
    }

    /**
     * Generate and download patient card with QR code
     */
    public function printCard(Patient $patient)
    {
        // Generate QR code with patient data
        $qrData = json_encode([
            'mr' => $patient->medical_record_number,
            'nik' => $patient->nik,
            'name' => $patient->name,
            'dob' => $patient->birth_date->format('Y-m-d'),
        ]);

        // Generate QR code as SVG with specific size
        $qrCode = QrCode::size(55)
            ->margin(1)
            ->generate($qrData);

        // Generate PDF with card size (credit card dimensions)
        $pdf = Pdf::loadView('patients.patient-card', compact('patient', 'qrCode'))
            ->setPaper([0, 0, 242.64, 153.07], 'portrait'); // 85.6mm x 53.98mm in points

        return $pdf->download('patient-card-' . $patient->medical_record_number . '.pdf');
    }
}
