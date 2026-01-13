<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Polyclinic;
use App\Http\Requests\StorePolyclinicRequest;
use App\Http\Requests\UpdatePolyclinicRequest;
use Illuminate\Http\Request;

class PolyclinicController extends Controller
{
    /**
     * Display a listing of polyclinics
     */
    public function index(Request $request)
    {
        $query = Polyclinic::query();

        // Search functionality
        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%");
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('is_active', $request->boolean('status'));
        }

        $polyclinics = $query->latest()->paginate(10)->withQueryString();

        return view('admin.polyclinics.index', compact('polyclinics'));
    }

    /**
     * Show the form for creating a new polyclinic
     */
    public function create()
    {
        return view('admin.polyclinics.create');
    }

    /**
     * Store a newly created polyclinic
     */
    public function store(StorePolyclinicRequest $request)
    {
        Polyclinic::create($request->validated());

        return redirect()->route('admin.polyclinics.index')
            ->with('success', 'Polyclinic created successfully.');
    }

    /**
     * Show the form for editing the polyclinic
     */
    public function edit(Polyclinic $polyclinic)
    {
        return view('admin.polyclinics.edit', compact('polyclinic'));
    }

    /**
     * Update the specified polyclinic
     */
    public function update(UpdatePolyclinicRequest $request, Polyclinic $polyclinic)
    {
        $polyclinic->update($request->validated());

        return redirect()->route('admin.polyclinics.index')
            ->with('success', 'Polyclinic updated successfully.');
    }

    /**
     * Remove the specified polyclinic
     */
    public function destroy(Polyclinic $polyclinic)
    {
        $polyclinic->delete();

        return redirect()->route('admin.polyclinics.index')
            ->with('success', 'Polyclinic deleted successfully.');
    }
}
