<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Registration Receipt - {{ $registration->registration_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
        }
        .header p {
            margin: 2px 0;
            font-size: 11px;
        }
        .queue-number {
            text-align: center;
            background: #f0f0f0;
            padding: 15px;
            margin: 20px 0;
            border: 2px solid #333;
        }
        .queue-number h2 {
            margin: 0 0 5px 0;
            font-size: 14px;
        }
        .queue-number .number {
            font-size: 32px;
            font-weight: bold;
            font-family: monospace;
        }
        .info-section {
            margin-bottom: 15px;
        }
        .info-section h3 {
            margin: 0 0 8px 0;
            font-size: 13px;
            font-weight: bold;
            border-bottom: 1px solid #ccc;
            padding-bottom: 3px;
        }
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 4px;
        }
        .info-label {
            display: table-cell;
            width: 40%;
            font-weight: bold;
        }
        .info-value {
            display: table-cell;
            width: 60%;
        }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            text-align: center;
            font-size: 10px;
        }
        .signature {
            margin-top: 40px;
            text-align: right;
        }
        .signature-line {
            display: inline-block;
            width: 200px;
            border-top: 1px solid #333;
            margin-top: 60px;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Hospital Header -->
    <div class="header">
        <h1>HOSPITAL MANAGEMENT SYSTEM</h1>
        <p>Jl. Kesehatan No. 123, Jakarta 12345</p>
        <p>Telp: (021) 1234-5678 | Email: info@hospital.com</p>
    </div>

    <!-- Receipt Title -->
    <div style="text-align: center; margin-bottom: 20px;">
        <h2 style="margin: 0;">OUTPATIENT REGISTRATION RECEIPT</h2>
        <p style="margin: 5px 0;">Registration Number: <strong>{{ $registration->registration_number }}</strong></p>
        <p style="margin: 5px 0;">Date: {{ $registration->registration_date->format('d F Y H:i') }}</p>
    </div>

    <!-- Queue Number Highlight -->
    <div class="queue-number">
        <h2>QUEUE NUMBER</h2>
        <div class="number">{{ $registration->queue_number }}</div>
    </div>

    <!-- QR Code Section -->
    <div style="text-align: center; margin: 20px 0; padding: 10px; background: #f9f9f9; border: 1px solid #ddd;">
        <div style="margin-bottom: 5px;">
            {!! $qrCode !!}
        </div>
        <p style="margin: 0; font-size: 10px; color: #666;">Scan for registration details</p>
    </div>

    <!-- Patient Information -->
    <div class="info-section">
        <h3>Patient Information</h3>
        <div class="info-row">
            <div class="info-label">Medical Record Number</div>
            <div class="info-value">: {{ $registration->patient->medical_record_number }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Name</div>
            <div class="info-value">: {{ $registration->patient->name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">NIK</div>
            <div class="info-value">: {{ $registration->patient->nik }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Birth Date / Age</div>
            <div class="info-value">: {{ $registration->patient->birth_date->format('d M Y') }} ({{ $registration->patient->birth_date->age }} years)</div>
        </div>
        <div class="info-row">
            <div class="info-label">Gender</div>
            <div class="info-value">: {{ ucfirst($registration->patient->gender) }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Phone</div>
            <div class="info-value">: {{ $registration->patient->phone }}</div>
        </div>
    </div>

    <!-- Registration Information -->
    <div class="info-section">
        <h3>Registration Information</h3>
        <div class="info-row">
            <div class="info-label">Polyclinic</div>
            <div class="info-value">: {{ $registration->polyclinic->name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Doctor</div>
            <div class="info-value">: {{ $registration->doctor->name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Specialization</div>
            <div class="info-value">: {{ $registration->doctor->specialization }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Payment Method</div>
            <div class="info-value">: {{ strtoupper($registration->payment_method) }}</div>
        </div>
    </div>

    <!-- Chief Complaint -->
    <div class="info-section">
        <h3>Chief Complaint</h3>
        <p style="margin: 5px 0;">{{ $registration->complaint }}</p>
    </div>

    <!-- Important Notes -->
    <div style="background: #f9f9f9; padding: 10px; margin: 20px 0; border-left: 3px solid #333;">
        <strong>Important Notes:</strong>
        <ul style="margin: 5px 0; padding-left: 20px;">
            <li>Please arrive 15 minutes before your scheduled time</li>
            <li>Bring this receipt and your ID card</li>
            <li>Queue numbers are called in order</li>
            <li>If you miss your queue, please inform the reception</li>
        </ul>
    </div>

    <!-- Signature -->
    <div class="signature">
        <p style="margin: 0;">Registration Officer</p>
        <div class="signature-line">
            <p style="margin: 0;">({{ auth()->user()->name }})</p>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>This is a computer-generated receipt. No signature required.</p>
        <p>Printed on: {{ now()->format('d F Y H:i:s') }}</p>
    </div>
</body>
</html>
