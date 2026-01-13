@extends('layouts.app')

@section('title', 'Edit Patient')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Edit Patient</h2>
        <p class="text-sm text-gray-600">Update patient information</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <form action="{{ route('patients.update', $patient) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Medical Record Number (Read-only) -->
            <div class="mb-6 bg-gray-50 border-l-4 border-gray-400 p-4 rounded">
                <p class="text-sm font-medium text-gray-700">Medical Record Number</p>
                <p class="text-lg font-mono font-bold text-gray-900">{{ $patient->medical_record_number }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- NIK -->
                <div class="md:col-span-2">
                    <label for="nik" class="block text-sm font-medium text-gray-700 mb-2">NIK (ID Number) *</label>
                    <input type="text" name="nik" id="nik" value="{{ old('nik', $patient->nik) }}" required maxlength="16" pattern="[0-9]{16}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nik') border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">16 digits</p>
                    @error('nik')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $patient->name) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Birth Place -->
                <div>
                    <label for="birth_place" class="block text-sm font-medium text-gray-700 mb-2">Birth Place</label>
                    <input type="text" name="birth_place" id="birth_place" value="{{ old('birth_place', $patient->birth_place) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('birth_place') border-red-500 @enderror">
                    @error('birth_place')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Birth Date -->
                <div>
                    <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-2">Birth Date *</label>
                    <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date', $patient->birth_date->format('Y-m-d')) }}" required max="{{ date('Y-m-d') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('birth_date') border-red-500 @enderror">
                    @error('birth_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Gender -->
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">Gender *</label>
                    <select name="gender" id="gender" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('gender') border-red-500 @enderror">
                        <option value="">Select Gender</option>
                        <option value="male" {{ old('gender', $patient->gender) === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $patient->gender) === 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                    @error('gender')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Blood Type -->
                <div>
                    <label for="blood_type" class="block text-sm font-medium text-gray-700 mb-2">Blood Type</label>
                    <select name="blood_type" id="blood_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('blood_type') border-red-500 @enderror">
                        <option value="">Unknown</option>
                        <option value="A" {{ old('blood_type', $patient->blood_type) === 'A' ? 'selected' : '' }}>A</option>
                        <option value="B" {{ old('blood_type', $patient->blood_type) === 'B' ? 'selected' : '' }}>B</option>
                        <option value="AB" {{ old('blood_type', $patient->blood_type) === 'AB' ? 'selected' : '' }}>AB</option>
                        <option value="O" {{ old('blood_type', $patient->blood_type) === 'O' ? 'selected' : '' }}>O</option>
                    </select>
                    @error('blood_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                    <input type="tel" name="phone" id="phone" value="{{ old('phone', $patient->phone) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Marital Status -->
                <div>
                    <label for="marital_status" class="block text-sm font-medium text-gray-700 mb-2">Marital Status</label>
                    <select name="marital_status" id="marital_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('marital_status') border-red-500 @enderror">
                        <option value="">Select Status</option>
                        <option value="single" {{ old('marital_status', $patient->marital_status) === 'single' ? 'selected' : '' }}>Single</option>
                        <option value="married" {{ old('marital_status', $patient->marital_status) === 'married' ? 'selected' : '' }}>Married</option>
                        <option value="divorced" {{ old('marital_status', $patient->marital_status) === 'divorced' ? 'selected' : '' }}>Divorced</option>
                        <option value="widowed" {{ old('marital_status', $patient->marital_status) === 'widowed' ? 'selected' : '' }}>Widowed</option>
                    </select>
                    @error('marital_status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Religion -->
                <div>
                    <label for="religion" class="block text-sm font-medium text-gray-700 mb-2">Religion</label>
                    <input type="text" name="religion" id="religion" value="{{ old('religion', $patient->religion) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('religion') border-red-500 @enderror">
                    @error('religion')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Occupation -->
                <div>
                    <label for="occupation" class="block text-sm font-medium text-gray-700 mb-2">Occupation</label>
                    <input type="text" name="occupation" id="occupation" value="{{ old('occupation', $patient->occupation) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('occupation') border-red-500 @enderror">
                    @error('occupation')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address -->
                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address *</label>
                    <textarea name="address" id="address" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('address') border-red-500 @enderror">{{ old('address', $patient->address) }}</textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Emergency Contact Name -->
                <div>
                    <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 mb-2">Emergency Contact Name</label>
                    <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="{{ old('emergency_contact_name', $patient->emergency_contact_name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('emergency_contact_name') border-red-500 @enderror">
                    @error('emergency_contact_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Emergency Contact Phone -->
                <div>
                    <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700 mb-2">Emergency Contact Phone</label>
                    <input type="tel" name="emergency_contact_phone" id="emergency_contact_phone" value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('emergency_contact_phone') border-red-500 @enderror">
                    @error('emergency_contact_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('patients.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Update Patient</button>
            </div>
        </form>
    </div>
</div>
@endsection
