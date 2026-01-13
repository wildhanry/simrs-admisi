@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Inpatient Registration Details</h2>
                    <div class="flex gap-2">
                        <a href="{{ route('inpatient.print', $registration) }}" target="_blank" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition">
                            Print Admission Letter
                        </a>
                        @if($registration->status === 'active')
                            <form action="{{ route('inpatient.discharge', $registration) }}" method="POST" onsubmit="return confirm('Are you sure you want to discharge this patient? The bed will be released.')">
                                @csrf
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">
                                    Discharge Patient
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('inpatient.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition">
                            Back to List
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Registration Number Highlight -->
                <div class="bg-purple-50 border-l-4 border-purple-500 p-6 mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-purple-900 mb-2">Registration Number</h3>
                            <p class="text-3xl font-bold text-purple-600 font-mono">{{ $registration->registration_number }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-600">Bed Assignment</div>
                            <div class="text-2xl font-semibold text-gray-900">{{ $registration->ward->name }}</div>
                            <div class="text-lg font-semibold text-purple-600">Bed {{ $registration->bed->bed_number }}</div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Patient Information -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Patient Information</h3>
                        <div class="space-y-3">
                            <div>
                                <div class="text-sm text-gray-600">Medical Record Number</div>
                                <div class="font-semibold text-gray-900 font-mono">{{ $registration->patient->medical_record_number }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600">Name</div>
                                <div class="font-semibold text-gray-900">{{ $registration->patient->name }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600">NIK</div>
                                <div class="font-semibold text-gray-900">{{ $registration->patient->nik }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600">Birth Date / Age</div>
                                <div class="font-semibold text-gray-900">
                                    {{ $registration->patient->birth_date->format('d M Y') }} 
                                    ({{ $registration->patient->birth_date->age }} years)
                                </div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600">Gender</div>
                                <div class="font-semibold text-gray-900">{{ ucfirst($registration->patient->gender) }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600">Phone</div>
                                <div class="font-semibold text-gray-900">{{ $registration->patient->phone }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Registration Information -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Registration Information</h3>
                        <div class="space-y-3">
                            <div>
                                <div class="text-sm text-gray-600">Registration Date</div>
                                <div class="font-semibold text-gray-900">{{ $registration->registration_date->format('d M Y H:i') }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600">Ward</div>
                                <div class="font-semibold text-gray-900">{{ $registration->ward->name }} ({{ $registration->ward->class }})</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600">Bed</div>
                                <div class="font-semibold text-gray-900">Bed {{ $registration->bed->bed_number }} ({{ $registration->bed->bed_type }})</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600">Doctor</div>
                                <div class="font-semibold text-gray-900">{{ $registration->doctor->name }}</div>
                                <div class="text-sm text-gray-600">{{ $registration->doctor->specialization }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600">Payment Method</div>
                                <div class="font-semibold text-gray-900">{{ strtoupper($registration->payment_method) }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600">Status</div>
                                <div>
                                    @if($registration->status === 'active')
                                        <span class="px-3 py-1 text-sm font-semibold bg-green-100 text-green-800 rounded">Active</span>
                                    @elseif($registration->status === 'completed')
                                        <span class="px-3 py-1 text-sm font-semibold bg-blue-100 text-blue-800 rounded">Completed</span>
                                    @else
                                        <span class="px-3 py-1 text-sm font-semibold bg-gray-100 text-gray-800 rounded">Cancelled</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Diagnosis -->
                <div class="mt-6 bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">Diagnosis</h3>
                    <p class="text-gray-700 whitespace-pre-line">{{ $registration->diagnosis }}</p>
                </div>

                <!-- Admission Details -->
                <div class="mt-6 bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Admission Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <div class="text-sm text-gray-600">Planned Admission</div>
                            <div class="font-semibold text-gray-900">
                                {{ $registration->planned_admission_date ? $registration->planned_admission_date->format('d M Y') : 'Not set' }}
                            </div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">Estimated Discharge</div>
                            <div class="font-semibold text-gray-900">
                                {{ $registration->estimated_discharge_date ? $registration->estimated_discharge_date->format('d M Y') : 'Not set' }}
                            </div>
                        </div>
                        @if($registration->actual_discharge_date)
                        <div>
                            <div class="text-sm text-gray-600">Actual Discharge</div>
                            <div class="font-semibold text-gray-900">
                                {{ $registration->actual_discharge_date->format('d M Y H:i') }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Notes -->
                @if($registration->notes)
                <div class="mt-6 bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">Notes</h3>
                    <p class="text-gray-700 whitespace-pre-line">{{ $registration->notes }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
