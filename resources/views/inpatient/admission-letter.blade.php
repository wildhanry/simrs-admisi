<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admission Letter - {{ $registration->registration_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
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
        .title {
            text-align: center;
            margin: 20px 0;
        }
        .title h2 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
            text-decoration: underline;
        }
        .registration-info {
            background: #f5f5f5;
            padding: 15px;
            margin: 20px 0;
            border: 1px solid #ddd;
        }
        .registration-info h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            font-weight: bold;
        }
        .bed-info {
            text-align: center;
            background: #e8f4f8;
            padding: 15px;
            margin: 20px 0;
            border: 2px solid #0066cc;
        }
        .bed-info .bed-number {
            font-size: 28px;
            font-weight: bold;
            color: #0066cc;
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
        .important-notes {
            background: #fff9e6;
            padding: 10px;
            margin: 20px 0;
            border-left: 3px solid #ffcc00;
        }
        .footer {
            margin-top: 40px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
        }
        .signatures {
            margin-top: 40px;
            display: table;
            width: 100%;
        }
        .signature {
            display: table-cell;
            width: 50%;
            text-align: center;
        }
        .signature-line {
            display: inline-block;
            width: 150px;
            border-top: 1px solid #333;
            margin-top: 60px;
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

    <!-- Title -->
    <div class="title">
        <h2>ADMISSION LETTER</h2>
        <p>Registration Number: <strong>{{ $registration->registration_number }}</strong></p>
        <p>Date: {{ $registration->registration_date->format('d F Y H:i') }}</p>
    </div>

    <!-- Bed Assignment Highlight -->
    <div class="bed-info">
        <h3 style="margin: 0 0 10px 0;">BED ASSIGNMENT</h3>
        <div style="margin: 10px 0;">
            <strong>{{ $registration->ward->name }}</strong> ({{ $registration->ward->class }})
        </div>
        <div class="bed-number">BED {{ $registration->bed->bed_number }}</div>
        <div style="margin-top: 5px; font-size: 11px;">{{ $registration->bed->bed_type }}</div>
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
        <div class="info-row">
            <div class="info-label">Address</div>
            <div class="info-value">: {{ $registration->patient->address }}</div>
        </div>
    </div>

    <!-- Admission Information -->
    <div class="info-section">
        <h3>Admission Information</h3>
        <div class="info-row">
            <div class="info-label">Doctor in Charge</div>
            <div class="info-value">: {{ $registration->doctor->name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Specialization</div>
            <div class="info-value">: {{ $registration->doctor->specialization }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Ward</div>
            <div class="info-value">: {{ $registration->ward->name }} ({{ $registration->ward->class }})</div>
        </div>
        <div class="info-row">
            <div class="info-label">Bed Number</div>
            <div class="info-value">: {{ $registration->bed->bed_number }} ({{ $registration->bed->bed_type }})</div>
        </div>
        <div class="info-row">
            <div class="info-label">Planned Admission Date</div>
            <div class="info-value">: {{ $registration->planned_admission_date ? $registration->planned_admission_date->format('d M Y') : now()->format('d M Y') }}</div>
        </div>
        @if($registration->estimated_discharge_date)
        <div class="info-row">
            <div class="info-label">Estimated Discharge</div>
            <div class="info-value">: {{ $registration->estimated_discharge_date->format('d M Y') }}</div>
        </div>
        @endif
        <div class="info-row">
            <div class="info-label">Payment Method</div>
            <div class="info-value">: {{ strtoupper($registration->payment_method) }}</div>
        </div>
    </div>

    <!-- Diagnosis -->
    <div class="info-section">
        <h3>Diagnosis</h3>
        <p style="margin: 5px 0;">{{ $registration->diagnosis }}</p>
    </div>

    <!-- Notes -->
    @if($registration->notes)
    <div class="info-section">
        <h3>Additional Notes</h3>
        <p style="margin: 5px 0;">{{ $registration->notes }}</p>
    </div>
    @endif

    <!-- Important Notes -->
    <div class="important-notes">
        <strong>Important Instructions:</strong>
        <ul style="margin: 5px 0; padding-left: 20px;">
            <li>Please bring this admission letter and your ID card</li>
            <li>Report to the admission desk upon arrival</li>
            <li>Visiting hours: 10:00 - 12:00 and 16:00 - 20:00</li>
            <li>Maximum 2 visitors at a time</li>
            <li>Please inform the nurse if you have any allergies or special needs</li>
            <li>Personal belongings are the patient's responsibility</li>
        </ul>
    </div>

    <!-- QR Code Section -->
    <div style="text-align: center; margin: 20px 0; padding: 10px; background: #f9f9f9; border: 1px solid #ddd;">
        <div style="margin-bottom: 5px;">
            {!! $qrCode !!}
        </div>
        <p style="margin: 0; font-size: 10px; color: #666;">Scan for admission details</p>
    </div>

    <!-- Signatures -->
    <div class="signatures">
        <div class="signature">
            <p style="margin: 0;">Admission Officer</p>
            <div class="signature-line">
                <p style="margin: 0;">({{ auth()->user()->name }})</p>
            </div>
        </div>
        <div class="signature">
            <p style="margin: 0;">Doctor in Charge</p>
            <div class="signature-line">
                <p style="margin: 0;">({{ $registration->doctor->name }})</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p style="text-align: center; margin: 0; font-size: 10px;">
            This is a computer-generated document. No signature required.<br>
            Printed on: {{ now()->format('d F Y H:i:s') }}
        </p>
    </div>
</body>
</html>
