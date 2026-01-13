<?php

namespace Database\Seeders;

use App\Models\Ward;
use App\Models\Bed;
use Illuminate\Database\Seeder;

class WardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wards = [
            [
                'code' => 'WARD-VIP-01',
                'name' => 'Ruang VIP',
                'class' => 'VIP',
                'location' => 'Lantai 4, Gedung C',
                'beds' => 5,
            ],
            [
                'code' => 'WARD-I-01',
                'name' => 'Ruang Kelas I',
                'class' => 'I',
                'location' => 'Lantai 3, Gedung C',
                'beds' => 10,
            ],
            [
                'code' => 'WARD-II-01',
                'name' => 'Ruang Kelas II',
                'class' => 'II',
                'location' => 'Lantai 2, Gedung C',
                'beds' => 15,
            ],
            [
                'code' => 'WARD-III-01',
                'name' => 'Ruang Kelas III',
                'class' => 'III',
                'location' => 'Lantai 1, Gedung C',
                'beds' => 20,
            ],
        ];

        foreach ($wards as $wardData) {
            $bedCount = $wardData['beds'];
            unset($wardData['beds']);
            
            $ward = Ward::create(array_merge($wardData, [
                'description' => 'Ruang rawat inap ' . $wardData['name'],
                'is_active' => true,
            ]));

            // Create beds for this ward
            for ($i = 1; $i <= $bedCount; $i++) {
                Bed::create([
                    'ward_id' => $ward->id,
                    'bed_number' => str_pad($i, 2, '0', STR_PAD_LEFT),
                    'status' => 'available',
                ]);
            }
        }
    }
}
