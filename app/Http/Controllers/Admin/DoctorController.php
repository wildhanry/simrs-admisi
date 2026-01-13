<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    /**
     * Display a listing of doctors
     */
    public function index(Request $request)
    {
        $query = Doctor::query();

        // Search functionality
        if ($search = $request->input('search')) {
            $query->search($search);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('is_active', $request->boolean('status'));
        }

        $doctors = $query->latest()->paginate(10)->withQueryString();

        return view('admin.doctors.index', compact('doctors'));
    }

    /**
     * Show the form for creating a new doctor
     */
    public function create()
    {
        return view('admin.doctors.create');
    }

    /**
     * Store a newly created doctor
     */
    public function store(StoreDoctorRequest $request)
    {
        Doctor::create($request->validated());

        return redirect()->route('admin.doctors.index')
            ->with('success', 'Doctor created successfully.');
    }

    /**
     * Display the specified doctor
     */
    public function show(Doctor $doctor)
    {
        $doctor->load(['registrations' => function ($query) {
            $query->latest()->take(10);
        }]);

        return view('admin.doctors.show', compact('doctor'));
    }

    /**
     * Show the form for editing the doctor
     */
    public function edit(Doctor $doctor)
    {
        return view('admin.doctors.edit', compact('doctor'));
    }

    /**
     * Update the specified doctor
     */
    public function update(UpdateDoctorRequest $request, Doctor $doctor)
    {
        $doctor->update($request->validated());

        return redirect()->route('admin.doctors.index')
            ->with('success', 'Doctor updated successfully.');
    }

    /**
     * Remove the specified doctor
     */
    public function destroy(Doctor $doctor)
    {
        $doctor->delete();

        return redirect()->route('admin.doctors.index')
            ->with('success', 'Doctor deleted successfully.');
    }
}
