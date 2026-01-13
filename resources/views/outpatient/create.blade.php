@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Pendaftaran Rawat Jalan Baru</h2>
                    <a href="{{ route('outpatient.index') }}" class="text-gray-600 hover:text-gray-900">
                        ← Kembali ke Daftar
                    </a>
                </div>

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('outpatient.store') }}" id="registrationForm">
                    @csrf

                    <!-- Patient Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pasien *</label>
                        <div class="relative">
                            <input type="text" id="patientSearch" placeholder="Cari pasien berdasarkan nama atau nomor RM..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" autocomplete="off">
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
                                    <button type="button" onclick="clearPatient()" class="text-red-600 hover:text-red-800 text-sm">Ganti</button>
                                </div>
                            </div>
                        </div>
                        @error('patient_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Polyclinic Selection -->
                    <div class="mb-6">
                        <label for="polyclinic_id" class="block text-sm font-medium text-gray-700 mb-2">Poliklinik *</label>
                        <select name="polyclinic_id" id="polyclinic_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('polyclinic_id') border-red-500 @enderror">
                            <option value="">Pilih Poliklinik</option>
                            @foreach($polyclinics as $polyclinic)
                                <option value="{{ $polyclinic->id }}" {{ old('polyclinic_id') == $polyclinic->id ? 'selected' : '' }}>
                                    {{ $polyclinic->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('polyclinic_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Doctor Selection (Dynamic) -->
                    <div class="mb-6">
                        <label for="doctor_id" class="block text-sm font-medium text-gray-700 mb-2">Dokter *</label>
                        <select name="doctor_id" id="doctor_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('doctor_id') border-red-500 @enderror" disabled>
                            <option value="">Pilih poliklinik terlebih dahulu</option>
                        </select>
                        @error('doctor_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Hanya menampilkan dokter yang tersedia</p>
                    </div>

                    <!-- Chief Complaint -->
                    <div class="mb-6">
                        <label for="complaint" class="block text-sm font-medium text-gray-700 mb-2">Keluhan Utama *</label>
                        <textarea name="complaint" id="complaint" rows="4" required maxlength="500" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('complaint') border-red-500 @enderror" placeholder="Jelaskan keluhan utama pasien...">{{ old('complaint') }}</textarea>
                        @error('complaint')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Maksimal 500 karakter</p>
                    </div>

                    <!-- Payment Method -->
                    <div class="mb-6">
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran *</label>
                        <select name="payment_method" id="payment_method" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('payment_method') border-red-500 @enderror">
                            <option value="">Pilih Metode Pembayaran</option>
                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Tunai</option>
                            <option value="bpjs" {{ old('payment_method') == 'bpjs' ? 'selected' : '' }}>BPJS</option>
                            <option value="insurance" {{ old('payment_method') == 'insurance' ? 'selected' : '' }}>Asuransi</option>
                            <option value="company" {{ old('payment_method') == 'company' ? 'selected' : '' }}>Perusahaan</option>
                        </select>
                        @error('payment_method')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex gap-4">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition">
                            Daftar
                        </button>
                        <a href="{{ route('outpatient.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg transition">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Patient Search with AJAX
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
            .then(data => {
                displayPatientResults(data);
            })
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

// Hide results when clicking outside
document.addEventListener('click', function(e) {
    if (!patientSearch.contains(e.target) && !patientResults.contains(e.target)) {
        patientResults.classList.add('hidden');
    }
});

// Load doctors based on polyclinic selection
const polyclinicSelect = document.getElementById('polyclinic_id');
const doctorSelect = document.getElementById('doctor_id');

polyclinicSelect.addEventListener('change', function() {
    const polyclinicId = this.value;
    
    if (!polyclinicId) {
        doctorSelect.innerHTML = '<option value="">Select polyclinic first</option>';
        doctorSelect.disabled = true;
        return;
    }

    doctorSelect.disabled = true;
    doctorSelect.innerHTML = '<option value="">Loading doctors...</option>';

    fetch(`/outpatient/doctors?polyclinic_id=${polyclinicId}`)
        .then(response => response.json())
        .then(doctors => {
            if (doctors.length === 0) {
                doctorSelect.innerHTML = '<option value="">No available doctors</option>';
                doctorSelect.disabled = true;
                return;
            }

            let html = '<option value="">Select Doctor</option>';
            doctors.forEach(doctor => {
                html += `<option value="${doctor.id}">${doctor.name} - ${doctor.specialization}</option>`;
            });
            
            doctorSelect.innerHTML = html;
            doctorSelect.disabled = false;
        })
        .catch(error => {
            console.error('Error:', error);
            doctorSelect.innerHTML = '<option value="">Error loading doctors</option>';
            doctorSelect.disabled = true;
        });
});
</script>
@endsection
