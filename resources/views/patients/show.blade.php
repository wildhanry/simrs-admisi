@extends('layouts.app')

@section('title', 'Patient Details')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Patient Details</h2>
            <p class="text-sm text-gray-600">View complete patient information</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('patients.printCard', $patient) }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg">Print Card</a>
            <a href="{{ route('patients.edit', $patient) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Edit Patient</a>
            <a href="{{ route('patients.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg">Back to List</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Patient Information -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Patient Information</h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Medical Record Number</p>
                        <p class="mt-1 text-sm font-mono font-semibold text-blue-600">{{ $patient->medical_record_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">NIK</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $patient->nik }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-sm font-medium text-gray-500">Full Name</p>
                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $patient->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Birth Place</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $patient->birth_place ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Birth Date</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $patient->birth_date->format('d F Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Age</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $patient->age }} years old</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Gender</p>
                        <p class="mt-1 text-sm text-gray-900">{{ ucfirst($patient->gender) }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Blood Type</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $patient->blood_type ?? 'Unknown' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Marital Status</p>
                        <p class="mt-1 text-sm text-gray-900">{{ ucfirst($patient->marital_status ?? 'Not specified') }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Religion</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $patient->religion ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Occupation</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $patient->occupation ?? '-' }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-sm font-medium text-gray-500">Address</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $patient->address }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Phone Number</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $patient->phone }}</p>
                    </div>
                </div>

                @if($patient->emergency_contact_name || $patient->emergency_contact_phone)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-900 mb-3">Emergency Contact</h4>
                    <div class="grid grid-cols-2 gap-4">
                        @if($patient->emergency_contact_name)
                        <div>
                            <p class="text-sm font-medium text-gray-500">Name</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $patient->emergency_contact_name }}</p>
                        </div>
                        @endif
                        @if($patient->emergency_contact_phone)
                        <div>
                            <p class="text-sm font-medium text-gray-500">Phone</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $patient->emergency_contact_phone }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Recent Registrations -->
        <div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Visits</h3>
                
                @if($patient->registrations->count() > 0)
                    <div class="space-y-3">
                        @foreach($patient->registrations as $registration)
                        <div class="p-3 border border-gray-200 rounded-lg">
                            <p class="text-xs font-mono text-gray-500">{{ $registration->registration_number }}</p>
                            <p class="text-sm font-medium text-gray-900 mt-1">{{ $registration->type === 'outpatient' ? 'Outpatient' : 'Inpatient' }}</p>
                            <p class="text-xs text-gray-500">{{ $registration->registration_date->format('d M Y') }}</p>
                            <span class="inline-block mt-2 px-2 py-1 text-xs rounded-full
                                @if($registration->status === 'active') bg-green-100 text-green-800
                                @elseif($registration->status === 'completed') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($registration->status) }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 text-center py-4">No visits yet</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
