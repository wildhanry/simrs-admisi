@extends('layouts.admin')

@section('title', 'Edit Bed')
@section('page-title', 'Edit Bed')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form action="{{ route('admin.beds.update', $bed) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6">
                <!-- Ward -->
                <div>
                    <label for="ward_id" class="block text-sm font-medium text-gray-700 mb-2">Ward *</label>
                    <select name="ward_id" id="ward_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('ward_id') border-red-500 @enderror">
                        <option value="">Select Ward</option>
                        @foreach($wards as $ward)
                            <option value="{{ $ward->id }}" {{ old('ward_id', $bed->ward_id) == $ward->id ? 'selected' : '' }}>
                                {{ $ward->name }} ({{ $ward->class }})
                            </option>
                        @endforeach
                    </select>
                    @error('ward_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bed Number -->
                <div>
                    <label for="bed_number" class="block text-sm font-medium text-gray-700 mb-2">Bed Number *</label>
                    <input type="text" name="bed_number" id="bed_number" value="{{ old('bed_number', $bed->bed_number) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('bed_number') border-red-500 @enderror">
                    @error('bed_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select name="status" id="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror">
                        <option value="available" {{ old('status', $bed->status) === 'available' ? 'selected' : '' }}>Available</option>
                        <option value="occupied" {{ old('status', $bed->status) === 'occupied' ? 'selected' : '' }}>Occupied</option>
                        <option value="maintenance" {{ old('status', $bed->status) === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="reserved" {{ old('status', $bed->status) === 'reserved' ? 'selected' : '' }}>Reserved</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('admin.beds.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Update Bed</button>
            </div>
        </form>
    </div>
</div>
@endsection
