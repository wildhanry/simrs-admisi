<?php

namespace Database\Seeders;

use App\Models\Polyclinic;
use Illuminate\Database\Seeder;

class PolyclinicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $polyclinics = [
            [
                'code' => 'POLI-001',
                'name' => 'Poli Umum',
                'location' => 'Lantai 1, Gedung A',
                'description' => 'Pelayanan kesehatan umum',
                'is_active' => true,
            ],
            [
                'code' => 'POLI-002',
                'name' => 'Poli Anak',
                'location' => 'Lantai 1, Gedung A',
                'description' => 'Pelayanan kesehatan anak',
                'is_active' => true,
            ],
            [
                'code' => 'POLI-003',
                'name' => 'Poli Kandungan',
                'location' => 'Lantai 2, Gedung A',
                'description' => 'Pelayanan kesehatan kandungan dan kebidanan',
                'is_active' => true,
            ],
            [
                'code' => 'POLI-004',
                'name' => 'Poli Jantung',
                'location' => 'Lantai 2, Gedung B',
                'description' => 'Pelayanan kesehatan jantung dan pembuluh darah',
                'is_active' => true,
            ],
            [
                'code' => 'POLI-005',
                'name' => 'Poli Bedah',
                'location' => 'Lantai 3, Gedung B',
                'description' => 'Pelayanan kesehatan bedah',
                'is_active' => true,
            ],
        ];

        foreach ($polyclinics as $polyclinic) {
            Polyclinic::create($polyclinic);
        }
    }
}
