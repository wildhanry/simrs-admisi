@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Registration Details</h2>
                    <div class="flex gap-2">
                        <a href="{{ route('outpatient.print', $registration->id) }}" target="_blank" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition">
                            Print Receipt
                        </a>
                        <a href="{{ route('outpatient.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition">
                            Back to List
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Queue Number Highlight -->
                <div class="bg-blue-50 border-l-4 border-blue-500 p-6 mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-blue-900 mb-2">Queue Number</h3>
                            <p class="text-4xl font-bold text-blue-600 font-mono">{{ $registration->queue_number }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-600">Registration Number</div>
                            <div class="text-lg font-semibold text-gray-900 font-mono">{{ $registration->registration_number }}</div>
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
                                <div class="text-sm text-gray-600">Polyclinic</div>
                                <div class="font-semibold text-gray-900">{{ $registration->polyclinic->name }}</div>
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

                <!-- Chief Complaint -->
                <div class="mt-6 bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">Chief Complaint</h3>
                    <p class="text-gray-700 whitespace-pre-line">{{ $registration->complaint }}</p>
                </div>

                <!-- Additional Information -->
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
