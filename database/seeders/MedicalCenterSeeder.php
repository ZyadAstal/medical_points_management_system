<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MedicalCenter;

class MedicalCenterSeeder extends Seeder
{
    public function run(): void
    {
        MedicalCenter::create([
            'name'     => 'Main Medical Center',
            'location' => 'Gaza - City Center',
            'phone'    => '0599000000',
        ]);

        MedicalCenter::create([
            'name'     => 'Branch Medical Center',
            'location' => 'Gaza - North',
            'phone'    => '0599111111',
        ]);
    }
}
