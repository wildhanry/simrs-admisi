@extends('layouts.admin')

@section('title', 'Edit Polyclinic')
@section('page-title', 'Edit Polyclinic')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form action="{{ route('admin.polyclinics.update', $polyclinic) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6">
                <!-- Code -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">Polyclinic Code *</label>
                    <input type="text" name="code" id="code" value="{{ old('code', $polyclinic->code) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('code') border-red-500 @enderror">
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Polyclinic Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $polyclinic->name) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $polyclinic->is_active) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('admin.polyclinics.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Update Polyclinic</button>
            </div>
        </form>
    </div>
</div>
@endsection
