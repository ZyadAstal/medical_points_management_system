<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MedicalCenter;

class MedicalCenterSeeder extends Seeder
{
    public function run(): void
    {
        $centers = [
            [
                'name'     => 'مستشفى غزة الأوروبي',
                'location' => 'خان يونس - شارع مصر المركزي',
                'phone'    => '082071555',
            ],
            [
                'name'     => 'مستشفى الشفاء',
                'location' => 'غزة - حي الرمال',
                'phone'    => '082863020',
            ],
            [
                'name'     => 'مستشفى ناصر',
                'location' => 'خان يونس - وسط المدينة',
                'phone'    => '082055777',
            ],
            [
                'name'     => 'مستشفى الأقصى',
                'location' => 'دير البلح - المنطقة الوسطى',
                'phone'    => '082539790',
            ],
        ];

        foreach ($centers as $center) {
            MedicalCenter::firstOrCreate(
                ['name' => $center['name']],
                $center
            );
        }
    }
}
