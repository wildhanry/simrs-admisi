@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">New Inpatient Registration</h2>
                    <a href="{{ route('inpatient.index') }}" class="text-gray-600 hover:text-gray-900">
                        ← Back to List
                    </a>
                </div>

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Bed Availability Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-blue-900 mb-2">Available Beds by Ward</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($wards as $ward)
                            <div class="bg-white rounded p-3 border border-blue-100">
                                <div class="text-sm font-medium text-gray-700">{{ $ward->name }}</div>
                                <div class="text-xs text-gray-500">{{ $ward->class }}</div>
                                <div class="text-2xl font-bold text-blue-600 mt-1">{{ $ward->beds->count() }}</div>
                                <div class="text-xs text-gray-500">beds available</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <form method="POST" action="{{ route('inpatient.store') }}" id="registrationForm">
                    @csrf

                    <!-- Patient Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Patient *</label>
                        <div class="relative">
                            <input type="text" id="patientSearch" placeholder="Search patient by name or MR number..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" autocomplete="off">
                            <div id="patientResults" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg hidden mt-1 max-h-64 overflow-y-auto"></div>
                        </div>
                        <input type="hidden" name="patient_id" id="patient_id" value="{{ old('patient_id') }}">
                        <div id="selectedPatient" class="mt-2 hidden">
                            <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="font-semibold text-gray-900" id="patientName"></div>
                                        <div class="text-sm text-gray-600">
                                            <span id="patientMR"></span> | 
                                            <span id="patientNIK"></span>
                                        </div>
                                    </div>
                                    <button type="button" onclick="clearPatient()" class="text-red-600 hover:text-red-800 text-sm">Change</button>
                                </div>
                            </div>
                        </div>
                        @error('patient_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Ward Selection -->
                    <div class="mb-6">
                        <label for="ward_id" class="block text-sm font-medium text-gray-700 mb-2">Ward *</label>
                        <select id="ward_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                            <option value="">Select Ward</option>
                            @foreach($wards as $ward)
                                <option value="{{ $ward->id }}">{{ $ward->name }} ({{ $ward->class }}) - {{ $ward->beds->count() }} beds available</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Bed Selection (Dynamic) -->
                    <div class="mb-6">
                        <label for="bed_id" class="block text-sm font-medium text-gray-700 mb-2">Bed *</label>
                        <select name="bed_id" id="bed_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('bed_id') border-red-500 @enderror" disabled>
                            <option value="">Select ward first</option>
                        </select>
                        @error('bed_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Bed availability is checked in real-time</p>
                    </div>

                    <!-- Doctor Selection -->
                    <div class="mb-6">
                        <label for="doctor_id" class="block text-sm font-medium text-gray-700 mb-2">Doctor *</label>
                        <select name="doctor_id" id="doctor_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('doctor_id') border-red-500 @enderror">
                            <option value="">Select Doctor</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                    {{ $doctor->name }} - {{ $doctor->specialization }}
                                </option>
                            @endforeach
                        </select>
                        @error('doctor_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Diagnosis -->
                    <div class="mb-6">
                        <label for="diagnosis" class="block text-sm font-medium text-gray-700 mb-2">Diagnosis *</label>
                        <textarea name="diagnosis" id="diagnosis" rows="3" required maxlength="500" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('diagnosis') border-red-500 @enderror" placeholder="Enter diagnosis...">{{ old('diagnosis') }}</textarea>
                        @error('diagnosis')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Maximum 500 characters</p>
                    </div>

                    <!-- Date Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="planned_admission_date" class="block text-sm font-medium text-gray-700 mb-2">Planned Admission Date</label>
                            <input type="date" name="planned_admission_date" id="planned_admission_date" value="{{ old('planned_admission_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('planned_admission_date') border-red-500 @enderror">
                            @error('planned_admission_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="estimated_discharge_date" class="block text-sm font-medium text-gray-700 mb-2">Estimated Discharge Date</label>
                            <input type="date" name="estimated_discharge_date" id="estimated_discharge_date" value="{{ old('estimated_discharge_date') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('estimated_discharge_date') border-red-500 @enderror">
                            @error('estimated_discharge_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="mb-6">
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">Payment Method *</label>
                        <select name="payment_method" id="payment_method" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('payment_method') border-red-500 @enderror">
                            <option value="">Select Payment Method</option>
                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="bpjs" {{ old('payment_method') == 'bpjs' ? 'selected' : '' }}>BPJS</option>
                            <option value="insurance" {{ old('payment_method') == 'insurance' ? 'selected' : '' }}>Insurance</option>
                            <option value="company" {{ old('payment_method') == 'company' ? 'selected' : '' }}>Company</option>
                        </select>
                        @error('payment_method')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="mb-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" id="notes" rows="3" maxlength="1000" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('notes') border-red-500 @enderror" placeholder="Additional notes (optional)...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex gap-4">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition">
                            Create Registration
                        </button>
                        <a href="{{ route('inpatient.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg transition">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Patient Search with AJAX (reused from outpatient)
let searchTimeout;
const patientSearch = document.getElementById('patientSearch');
const patientResults = document.getElementById('patientResults');
const patientIdInput = document.getElementById('patient_id');
const selectedPatientDiv = document.getElementById('selectedPatient');

patientSearch.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const query = this.value.trim();

    if (query.length < 2) {
        patientResults.classList.add('hidden');
        return;
    }

    searchTimeout = setTimeout(() => {
        fetch(`/patients-search?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => displayPatientResults(data))
            .catch(error => console.error('Error:', error));
    }, 300);
});

function displayPatientResults(patients) {
    if (patients.length === 0) {
        patientResults.innerHTML = '<div class="p-3 text-gray-500 text-sm">No patients found</div>';
        patientResults.classList.remove('hidden');
        return;
    }

    let html = '';
    patients.forEach(patient => {
        const age = calculateAge(patient.birth_date);
        html += `
            <div class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100" onclick="selectPatient(${patient.id}, '${patient.name}', '${patient.medical_record_number}', '${patient.nik}')">
                <div class="font-semibold text-sm">${patient.name}</div>
                <div class="text-xs text-gray-600">
                    ${patient.medical_record_number} | NIK: ${patient.nik} | ${age} years | ${patient.gender}
                </div>
            </div>
        `;
    });

    patientResults.innerHTML = html;
    patientResults.classList.remove('hidden');
}

function selectPatient(id, name, mr, nik) {
    patientIdInput.value = id;
    document.getElementById('patientName').textContent = name;
    document.getElementById('patientMR').textContent = mr;
    document.getElementById('patientNIK').textContent = 'NIK: ' + nik;
    
    selectedPatientDiv.classList.remove('hidden');
    patientSearch.value = '';
    patientResults.classList.add('hidden');
}

function clearPatient() {
    patientIdInput.value = '';
    selectedPatientDiv.classList.add('hidden');
}

function calculateAge(birthDate) {
    const today = new Date();
    const birth = new Date(birthDate);
    let age = today.getFullYear() - birth.getFullYear();
    const monthDiff = today.getMonth() - birth.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
        age--;
    }
    return age;
}

document.addEventListener('click', function(e) {
    if (!patientSearch.contains(e.target) && !patientResults.contains(e.target)) {
        patientResults.classList.add('hidden');
    }
});

// Load beds based on ward selection
const wardSelect = document.getElementById('ward_id');
const bedSelect = document.getElementById('bed_id');

wardSelect.addEventListener('change', function() {
    const wardId = this.value;
    
    if (!wardId) {
        bedSelect.innerHTML = '<option value="">Select ward first</option>';
        bedSelect.disabled = true;
        return;
    }

    bedSelect.disabled = true;
    bedSelect.innerHTML = '<option value="">Loading beds...</option>';

    fetch(`/inpatient/beds?ward_id=${wardId}`)
        .then(response => response.json())
        .then(beds => {
            if (beds.length === 0) {
                bedSelect.innerHTML = '<option value="">No available beds</option>';
                bedSelect.disabled = true;
                return;
            }

            let html = '<option value="">Select Bed</option>';
            beds.forEach(bed => {
                html += `<option value="${bed.id}">Bed ${bed.bed_number} (${bed.bed_type})</option>`;
            });
            
            bedSelect.innerHTML = html;
            bedSelect.disabled = false;
        })
        .catch(error => {
            console.error('Error:', error);
            bedSelect.innerHTML = '<option value="">Error loading beds</option>';
            bedSelect.disabled = true;
        });
});

// Check bed availability before submit (optional real-time check)
bedSelect.addEventListener('change', function() {
    const bedId = this.value;
    
    if (!bedId) return;

    // Optional: Check bed availability in real-time
    fetch(`/inpatient/check-bed?bed_id=${bedId}`)
        .then(response => response.json())
        .then(data => {
            if (!data.available) {
                alert(data.message);
                bedSelect.value = '';
            }
        })
        .catch(error => console.error('Error:', error));
});
</script>
@endsection
