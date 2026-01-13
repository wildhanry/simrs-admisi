<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ward;
use App\Http\Requests\StoreWardRequest;
use App\Http\Requests\UpdateWardRequest;
use Illuminate\Http\Request;

class WardController extends Controller
{
    /**
     * Display a listing of wards
     */
    public function index(Request $request)
    {
        $query = Ward::withCount(['beds', 'availableBeds']);

        // Search functionality
        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%");
        }

        // Filter by class
        if ($class = $request->input('class')) {
            $query->where('class', $class);
        }

        $wards = $query->latest()->paginate(10)->withQueryString();

        return view('admin.wards.index', compact('wards'));
    }

    /**
     * Show the form for creating a new ward
     */
    public function create()
    {
        return view('admin.wards.create');
    }

    /**
     * Store a newly created ward
     */
    public function store(StoreWardRequest $request)
    {
        Ward::create($request->validated());

        return redirect()->route('admin.wards.index')
            ->with('success', 'Ward created successfully.');
    }

    /**
     * Show the form for editing the ward
     */
    public function edit(Ward $ward)
    {
        return view('admin.wards.edit', compact('ward'));
    }

    /**
     * Update the specified ward
     */
    public function update(UpdateWardRequest $request, Ward $ward)
    {
        $ward->update($request->validated());

        return redirect()->route('admin.wards.index')
            ->with('success', 'Ward updated successfully.');
    }

    /**
     * Remove the specified ward
     */
    public function destroy(Ward $ward)
    {
        $ward->delete();

        return redirect()->route('admin.wards.index')
            ->with('success', 'Ward deleted successfully.');
    }
}
