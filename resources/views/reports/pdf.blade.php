<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Registration Report</title>
    <style>
        @page {
            margin: 15mm;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }
        .header h1 {
            font-size: 18px;
            margin-bottom: 3px;
        }
        .header h2 {
            font-size: 14px;
            font-weight: normal;
            color: #666;
        }
        .header p {
            font-size: 9px;
            color: #888;
            margin-top: 3px;
        }
        .filters {
            background: #f5f5f5;
            padding: 8px;
            margin-bottom: 12px;
            border-radius: 3px;
        }
        .filters h3 {
            font-size: 11px;
            margin-bottom: 5px;
        }
        .filters p {
            font-size: 9px;
            margin-bottom: 2px;
        }
        .stats {
            margin-bottom: 12px;
            display: table;
            width: 100%;
        }
        .stat-box {
            display: table-cell;
            padding: 6px;
            text-align: center;
            border: 1px solid #ddd;
            background: #f9f9f9;
        }
        .stat-box .label {
            font-size: 9px;
            color: #666;
        }
        .stat-box .value {
            font-size: 16px;
            font-weight: bold;
            margin-top: 2px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th {
            background: #333;
            color: white;
            padding: 6px 4px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
        }
        td {
            padding: 5px 4px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 9px;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-outpatient {
            background: #dbeafe;
            color: #1e40af;
        }
        .badge-inpatient {
            background: #e9d5ff;
            color: #6b21a8;
        }
        .badge-active {
            background: #d1fae5;
            color: #065f46;
        }
        .badge-completed {
            background: #dbeafe;
            color: #1e40af;
        }
        .badge-cancelled {
            background: #e5e7eb;
            color: #374151;
        }
        .footer {
            margin-top: 15px;
            padding-top: 8px;
            border-top: 1px solid #ccc;
            text-align: center;
            font-size: 8px;
            color: #888;
        }
        .no-data {
            text-align: center;
            padding: 30px;
            color: #999;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>SIMRS ADMISI</h1>
        <h2>Registration Report</h2>
        <p>Generated on: {{ now()->format('d F Y H:i:s') }}</p>
    </div>

    <!-- Filters Applied -->
    @if($filters['start_date'] || $filters['end_date'] || $filters['type'] || $filters['status'] || $filters['payment_method'])
    <div class="filters">
        <h3>Filters Applied:</h3>
        @if($filters['start_date'])
        <p><strong>Start Date:</strong> {{ \Carbon\Carbon::parse($filters['start_date'])->format('d M Y') }}</p>
        @endif
        @if($filters['end_date'])
        <p><strong>End Date:</strong> {{ \Carbon\Carbon::parse($filters['end_date'])->format('d M Y') }}</p>
        @endif
        @if($filters['type'])
        <p><strong>Type:</strong> {{ ucfirst($filters['type']) }}</p>
        @endif
        @if($filters['status'])
        <p><strong>Status:</strong> {{ ucfirst($filters['status']) }}</p>
        @endif
        @if($filters['payment_method'])
        <p><strong>Payment Method:</strong> {{ strtoupper($filters['payment_method']) }}</p>
        @endif
    </div>
    @endif

    <!-- Statistics -->
    <div class="stats">
        <div class="stat-box">
            <div class="label">Total</div>
            <div class="value">{{ $stats['total'] }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Outpatient</div>
            <div class="value">{{ $stats['outpatient'] }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Inpatient</div>
            <div class="value">{{ $stats['inpatient'] }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Active</div>
            <div class="value">{{ $stats['active'] }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Completed</div>
            <div class="value">{{ $stats['completed'] }}</div>
        </div>
    </div>

    <!-- Payment Statistics -->
    <div class="stats" style="margin-bottom: 15px;">
        <div class="stat-box">
            <div class="label">Cash</div>
            <div class="value" style="font-size: 14px;">{{ $stats['cash'] }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Insurance</div>
            <div class="value" style="font-size: 14px;">{{ $stats['insurance'] }}</div>
        </div>
        <div class="stat-box">
            <div class="label">BPJS</div>
            <div class="value" style="font-size: 14px;">{{ $stats['bpjs'] }}</div>
        </div>
    </div>

    <!-- Registration Table -->
    @if($registrations->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 8%;">Date</th>
                <th style="width: 13%;">Reg Number</th>
                <th style="width: 15%;">Patient</th>
                <th style="width: 8%;">Type</th>
                <th style="width: 15%;">Doctor</th>
                <th style="width: 15%;">Service</th>
                <th style="width: 8%;">Payment</th>
                <th style="width: 8%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($registrations as $registration)
            <tr>
                <td>{{ $registration->registration_date->format('d M Y') }}</td>
                <td>
                    <div style="font-family: monospace; font-size: 8px;">{{ $registration->registration_number }}</div>
                    @if($registration->type === 'outpatient' && $registration->queue_number)
                    <div style="font-size: 8px; color: #666;">Q: {{ $registration->queue_number }}</div>
                    @endif
                </td>
                <td>
                    <div style="font-weight: bold;">{{ $registration->patient->name }}</div>
                    <div style="font-size: 8px; color: #666;">{{ $registration->patient->medical_record_number }}</div>
                </td>
                <td>
                    <span class="badge badge-{{ $registration->type }}">
                        {{ strtoupper($registration->type) }}
                    </span>
                </td>
                <td>{{ $registration->doctor->name }}</td>
                <td>
                    @if($registration->type === 'outpatient')
                        {{ $registration->polyclinic->name ?? '-' }}
                    @else
                        {{ $registration->bed->ward->name ?? '-' }}<br>
                        <span style="font-size: 8px;">Bed: {{ $registration->bed->bed_number ?? '-' }}</span>
                    @endif
                </td>
                <td>{{ strtoupper($registration->payment_method) }}</td>
                <td>
                    <span class="badge badge-{{ $registration->status }}">
                        {{ strtoupper($registration->status) }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="text-align: right; font-size: 9px; margin-top: 10px;">
        <strong>Total Records: {{ $registrations->count() }}</strong>
    </div>
    @else
    <div class="no-data">
        <p>No registration data available for the selected filters.</p>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>SIMRS Admisi - Hospital Management System</p>
        <p>This is a computer-generated document.</p>
    </div>
</body>
</html>
