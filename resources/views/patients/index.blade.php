@extends('layouts.app')

@section('title', 'Patient Management')
@section('page-title', 'Patient Management')
@section('page-description', 'Manage patient records and medical history')

@section('content')
<div>
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Patient Management</h2>
            <p class="text-sm text-gray-600">Manage patient records and information</p>
        </div>
        <a href="{{ route('patients.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Add Patient
        </a>
    </div>

    <!-- Search with AJAX -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="relative">
            <input 
                type="text" 
                id="patient-search" 
                placeholder="Search by name or medical record number..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
            <div id="search-results" class="absolute w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg hidden z-10 max-h-96 overflow-y-auto">
                <!-- AJAX results will be inserted here -->
            </div>
        </div>
    </div>

    <!-- Patients Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. RM</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Info Pasien</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Kelamin</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usia</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontak</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($patients as $patient)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex px-2 py-1 text-xs font-mono font-semibold rounded bg-blue-100 text-blue-800">
                            {{ $patient->medical_record_number }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $patient->nik }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $patient->name }}</div>
                            <div class="text-sm text-gray-500">{{ $patient->birth_place }}, {{ $patient->birth_date->format('d M Y') }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($patient->gender === 'male')
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Laki-laki</span>
                        @else
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-pink-100 text-pink-800">Perempuan</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $patient->age }} tahun
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $patient->phone }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('patients.show', $patient) }}" class="text-green-600 hover:text-green-900 mr-3">Lihat</a>
                        <a href="{{ route('patients.printCard', $patient) }}" class="text-purple-600 hover:text-purple-900 mr-3">Kartu</a>
                        <a href="{{ route('patients.edit', $patient) }}" class="text-blue-600 hover:text-blue-900 mr-3">Ubah</a>
                        <form action="{{ route('patients.destroy', $patient) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pasien ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <p class="text-lg">No patients found</p>
                        <p class="text-sm mt-1">Get started by adding a new patient.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($patients->hasPages())
        <div class="mt-6">
            {{ $patients->links() }}
        </div>
    @endif
</div>

<!-- AJAX Search Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('patient-search');
    const searchResults = document.getElementById('search-results');
    let debounceTimer;

    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const query = this.value.trim();

        if (query.length < 2) {
            searchResults.classList.add('hidden');
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`{{ route('patients.search') }}?q=${encodeURIComponent(query)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    displayResults(data);
                } else {
                    searchResults.innerHTML = '<div class="p-4 text-gray-500 text-sm">No patients found</div>';
                    searchResults.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Search error:', error);
            });
        }, 300);
    });

    function displayResults(patients) {
        let html = '<div class="divide-y divide-gray-200">';
        
        patients.forEach(patient => {
            const birthDate = new Date(patient.birth_date);
            const age = Math.floor((new Date() - birthDate) / (365.25 * 24 * 60 * 60 * 1000));
            
            html += `
                <a href="/patients/${patient.id}" class="block p-4 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center mb-1">
                                <span class="text-xs font-mono font-semibold bg-blue-100 text-blue-800 px-2 py-1 rounded mr-2">
                                    ${patient.medical_record_number}
                                </span>
                                <span class="text-xs ${patient.gender === 'male' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800'} px-2 py-1 rounded">
                                    ${patient.gender === 'male' ? 'Male' : 'Female'}
                                </span>
                            </div>
                            <div class="text-sm font-medium text-gray-900">${patient.name}</div>
                            <div class="text-xs text-gray-500">NIK: ${patient.nik} • ${age} years • ${patient.phone || '-'}</div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
            `;
        });
        
        html += '</div>';
        searchResults.innerHTML = html;
        searchResults.classList.remove('hidden');
    }

    // Hide results when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.add('hidden');
        }
    });
});
</script>
@endsection
