@extends('layouts.app')

@section('title', 'Laporan Pendaftaran')
@section('page-title', 'Laporan')
@section('page-description', 'Lihat dan ekspor data pendaftaran')

@section('content')
<div>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Laporan Pendaftaran</h2>
        <p class="text-sm text-gray-600">Lihat dan ekspor data pendaftaran dengan filter</p>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('reports.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Start Date -->
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                    <input type="date" 
                        name="start_date" 
                        id="start_date" 
                        value="{{ request('start_date') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- End Date -->
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                    <input type="date" 
                        name="end_date" 
                        id="end_date" 
                        value="{{ request('end_date') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Jenis</label>
                    <select name="type" 
                        id="type" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Jenis</option>
                        <option value="outpatient" {{ request('type') == 'outpatient' ? 'selected' : '' }}>Rawat Jalan</option>
                        <option value="inpatient" {{ request('type') == 'inpatient' ? 'selected' : '' }}>Rawat Inap</option>
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" 
                        id="status" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Batal</option>
                    </select>
                </div>

                <!-- Payment Method -->
                <div>
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                    <select name="payment_method" 
                        id="payment_method" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Metode</option>
                        <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Tunai</option>
                        <option value="insurance" {{ request('payment_method') == 'insurance' ? 'selected' : '' }}>Asuransi</option>
                        <option value="bpjs" {{ request('payment_method') == 'bpjs' ? 'selected' : '' }}>BPJS</option>
                    </select>
                </div>
            </div>

            <!-- Filter Buttons -->
            <div class="flex space-x-3">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                    Terapkan Filter
                </button>
                <a href="{{ route('reports.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg">
                    Reset
                </a>
                @if(request()->hasAny(['start_date', 'end_date', 'type', 'status', 'payment_method']))
                <button type="button" 
                    onclick="exportPdf()"
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">
                    Export to PDF
                </button>
                @endif
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-4">
            <p class="text-sm text-gray-600">Total</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4">
            <p class="text-sm text-gray-600">Outpatient</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['outpatient'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4">
            <p class="text-sm text-gray-600">Inpatient</p>
            <p class="text-2xl font-bold text-purple-600">{{ $stats['inpatient'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4">
            <p class="text-sm text-gray-600">Active</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4">
            <p class="text-sm text-gray-600">Completed</p>
            <p class="text-2xl font-bold text-gray-600">{{ $stats['completed'] }}</p>
        </div>
    </div>

    <!-- Registration Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reg Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($registrations as $registration)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $registration->registration_date->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-xs font-mono text-gray-500">{{ $registration->registration_number }}</p>
                            @if($registration->type === 'outpatient' && $registration->queue_number)
                            <p class="text-xs text-blue-600">Queue: {{ $registration->queue_number }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-sm font-medium text-gray-900">{{ $registration->patient->name }}</p>
                            <p class="text-xs text-gray-500">{{ $registration->patient->medical_record_number }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($registration->type === 'outpatient') bg-blue-100 text-blue-800
                                @else bg-purple-100 text-purple-800
                                @endif">
                                {{ ucfirst($registration->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $registration->doctor->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($registration->type === 'outpatient')
                                {{ $registration->polyclinic->name ?? '-' }}
                            @else
                                {{ $registration->bed->ward->name ?? '-' }} - {{ $registration->bed->bed_number ?? '-' }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                {{ strtoupper($registration->payment_method) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($registration->status === 'active') bg-green-100 text-green-800
                                @elseif($registration->status === 'completed') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($registration->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-sm text-gray-500">
                            No registrations found. Try adjusting your filters.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($registrations->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $registrations->links() }}
        </div>
        @endif
    </div>
</div>

<script>
function exportPdf() {
    const params = new URLSearchParams(window.location.search);
    window.location.href = '{{ route("reports.exportPdf") }}?' + params.toString();
}
</script>
@endsection
