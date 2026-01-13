<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bed;
use App\Models\Ward;
use App\Http\Requests\StoreBedRequest;
use App\Http\Requests\UpdateBedRequest;
use Illuminate\Http\Request;

class BedController extends Controller
{
    /**
     * Display a listing of beds
     */
    public function index(Request $request)
    {
        $query = Bed::with('ward');

        // Search functionality
        if ($search = $request->input('search')) {
            $query->where('bed_number', 'like', "%{$search}%")
                ->orWhereHas('ward', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
        }

        // Filter by ward
        if ($wardId = $request->input('ward_id')) {
            $query->where('ward_id', $wardId);
        }

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $beds = $query->latest()->paginate(15)->withQueryString();
        $wards = Ward::active()->get();

        return view('admin.beds.index', compact('beds', 'wards'));
    }

    /**
     * Show the form for creating a new bed
     */
    public function create()
    {
        $wards = Ward::active()->get();
        return view('admin.beds.create', compact('wards'));
    }

    /**
     * Store a newly created bed
     */
    public function store(StoreBedRequest $request)
    {
        Bed::create($request->validated());

        return redirect()->route('admin.beds.index')
            ->with('success', 'Bed created successfully.');
    }

    /**
     * Show the form for editing the bed
     */
    public function edit(Bed $bed)
    {
        $wards = Ward::active()->get();
        return view('admin.beds.edit', compact('bed', 'wards'));
    }

    /**
     * Update the specified bed
     */
    public function update(UpdateBedRequest $request, Bed $bed)
    {
        $bed->update($request->validated());

        return redirect()->route('admin.beds.index')
            ->with('success', 'Bed updated successfully.');
    }

    /**
     * Remove the specified bed
     */
    public function destroy(Bed $bed)
    {
        $bed->delete();

        return redirect()->route('admin.beds.index')
            ->with('success', 'Bed deleted successfully.');
    }
}
