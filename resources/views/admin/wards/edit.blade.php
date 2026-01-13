@extends('layouts.admin')

@section('title', 'Edit Ward')
@section('page-title', 'Edit Ward')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form action="{{ route('admin.wards.update', $ward) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6">
                <!-- Code -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">Ward Code *</label>
                    <input type="text" name="code" id="code" value="{{ old('code', $ward->code) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('code') border-red-500 @enderror">
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Ward Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $ward->name) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Class -->
                <div>
                    <label for="class" class="block text-sm font-medium text-gray-700 mb-2">Ward Class *</label>
                    <select name="class" id="class" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('class') border-red-500 @enderror">
                        <option value="">Select Class</option>
                        <option value="VIP" {{ old('class', $ward->class) === 'VIP' ? 'selected' : '' }}>VIP</option>
                        <option value="Class 1" {{ old('class', $ward->class) === 'Class 1' ? 'selected' : '' }}>Class 1</option>
                        <option value="Class 2" {{ old('class', $ward->class) === 'Class 2' ? 'selected' : '' }}>Class 2</option>
                        <option value="Class 3" {{ old('class', $ward->class) === 'Class 3' ? 'selected' : '' }}>Class 3</option>
                    </select>
                    @error('class')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Building -->
                <div>
                    <label for="building" class="block text-sm font-medium text-gray-700 mb-2">Building</label>
                    <input type="text" name="building" id="building" value="{{ old('building', $ward->building) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('building') border-red-500 @enderror">
                    @error('building')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Floor -->
                <div>
                    <label for="floor" class="block text-sm font-medium text-gray-700 mb-2">Floor</label>
                    <input type="number" name="floor" id="floor" value="{{ old('floor', $ward->floor) }}" min="1" max="20" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('floor') border-red-500 @enderror">
                    @error('floor')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('admin.wards.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Update Ward</button>
            </div>
        </form>
    </div>
</div>
@endsection
