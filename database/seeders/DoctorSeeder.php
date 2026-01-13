<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Polyclinic;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get polyclinics
        $umum = Polyclinic::where('code', 'UMUM')->first();
        $anak = Polyclinic::where('code', 'ANAK')->first();
        $kandungan = Polyclinic::where('code', 'OBGYN')->first();
        $jantung = Polyclinic::where('code', 'JANTUNG')->first();
        $bedah = Polyclinic::where('code', 'BEDAH')->first();
        
        $doctors = [
            [
                'sip_number' => 'SIP-001-2024',
                'name' => 'Dr. Ahmad Fauzi, Sp.PD',
                'specialization' => 'Penyakit Dalam',
                'polyclinic_id' => $umum?->id,
                'phone' => '081234567890',
                'email' => 'ahmad.fauzi@hospital.com',
                'is_active' => true,
            ],
            [
                'sip_number' => 'SIP-002-2024',
                'name' => 'Dr. Siti Nurhaliza, Sp.A',
                'specialization' => 'Anak',
                'polyclinic_id' => $anak?->id,
                'phone' => '081234567891',
                'email' => 'siti.nurhaliza@hospital.com',
                'is_active' => true,
            ],
            [
                'sip_number' => 'SIP-003-2024',
                'name' => 'Dr. Budi Santoso, Sp.OG',
                'specialization' => 'Kandungan',
                'polyclinic_id' => $kandungan?->id,
                'phone' => '081234567892',
                'email' => 'budi.santoso@hospital.com',
                'is_active' => true,
            ],
            [
                'sip_number' => 'SIP-004-2024',
                'name' => 'Dr. Dewi Lestari, Sp.JP',
                'specialization' => 'Jantung',
                'polyclinic_id' => $jantung?->id,
                'phone' => '081234567893',
                'email' => 'dewi.lestari@hospital.com',
                'is_active' => true,
            ],
            [
                'sip_number' => 'SIP-005-2024',
                'name' => 'Dr. Rizki Pratama, Sp.B',
                'specialization' => 'Bedah',
                'polyclinic_id' => $bedah?->id,
                'phone' => '081234567894',
                'email' => 'rizki.pratama@hospital.com',
                'is_active' => true,
            ],
        ];

        foreach ($doctors as $doctor) {
            Doctor::create($doctor);
        }
    }
}
