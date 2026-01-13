<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Patient Card - {{ $patient->medical_record_number }}</title>
    <style>
        @page {
            size: 85.6mm 53.98mm; /* Credit card size */
            margin: 0;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 8px;
            margin: 0;
            padding: 0;
            width: 85.6mm;
            height: 53.98mm;
        }
        .card {
            width: 100%;
            height: 100%;
            border: 2px solid #0066cc;
            box-sizing: border-box;
            position: relative;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-header {
            background: rgba(255, 255, 255, 0.95);
            padding: 4px 8px;
            border-bottom: 2px solid #0066cc;
        }
        .hospital-name {
            font-size: 11px;
            font-weight: bold;
            color: #0066cc;
            margin: 0;
            text-align: center;
        }
        .card-type {
            font-size: 7px;
            color: #666;
            margin: 0;
            text-align: center;
        }
        .card-body {
            display: table;
            width: 100%;
            padding: 6px 8px;
            background: rgba(255, 255, 255, 0.98);
        }
        .left-section {
            display: table-cell;
            width: 60%;
            vertical-align: top;
            padding-right: 5px;
        }
        .right-section {
            display: table-cell;
            width: 40%;
            vertical-align: top;
            text-align: center;
        }
        .mr-number {
            font-size: 10px;
            font-weight: bold;
            color: #0066cc;
            margin: 0 0 4px 0;
            font-family: 'Courier New', monospace;
        }
        .patient-name {
            font-size: 11px;
            font-weight: bold;
            color: #333;
            margin: 0 0 6px 0;
            text-transform: uppercase;
        }
        .info-row {
            margin: 2px 0;
            color: #555;
        }
        .info-label {
            display: inline-block;
            width: 40px;
            font-size: 7px;
            color: #666;
        }
        .info-value {
            font-size: 8px;
            font-weight: 600;
            color: #333;
        }
        .qr-code {
            margin: 2px 0;
        }
        .qr-code img {
            width: 55px;
            height: 55px;
            border: 1px solid #ddd;
            background: white;
        }
        .qr-label {
            font-size: 6px;
            color: #666;
            margin-top: 2px;
        }
        .card-footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            padding: 3px 8px;
            border-top: 1px solid #ddd;
            font-size: 6px;
            color: #999;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="card">
        <!-- Header -->
        <div class="card-header">
            <p class="hospital-name">🏥 HOSPITAL SYSTEM</p>
            <p class="card-type">PATIENT IDENTIFICATION CARD</p>
        </div>

        <!-- Body -->
        <div class="card-body">
            <!-- Left Section: Patient Info -->
            <div class="left-section">
                <p class="mr-number">{{ $patient->medical_record_number }}</p>
                <p class="patient-name">{{ $patient->name }}</p>
                
                <div class="info-row">
                    <span class="info-label">NIK</span>
                    <span class="info-value">: {{ $patient->nik }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">DOB</span>
                    <span class="info-value">: {{ $patient->birth_date->format('d/m/Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Age</span>
                    <span class="info-value">: {{ $patient->birth_date->age }} years</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Gender</span>
                    <span class="info-value">: {{ strtoupper($patient->gender) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Blood</span>
                    <span class="info-value">: {{ $patient->blood_type ? strtoupper($patient->blood_type) : 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Phone</span>
                    <span class="info-value">: {{ $patient->phone }}</span>
                </div>
            </div>

            <!-- Right Section: QR Code -->
            <div class="right-section">
                <div class="qr-code">
                    {!! $qrCode !!}
                </div>
                <p class="qr-label">Scan for details</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="card-footer">
            Valid from {{ now()->format('d M Y') }} | For hospital use only | Emergency: (021) 1234-5678
        </div>
    </div>
</body>
</html>
