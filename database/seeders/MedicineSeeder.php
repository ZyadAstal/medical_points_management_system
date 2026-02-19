<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medicine;

class MedicineSeeder extends Seeder
{
    public function run(): void
    {
        $medicines = [
            ['name' => 'باراسيتامول ٥٠٠ ملغ',   'points_cost' => 5,  'expiry_date' => '2027-06-30'],
            ['name' => 'إيبوبروفين ٤٠٠ ملغ',     'points_cost' => 8,  'expiry_date' => '2027-03-15'],
            ['name' => 'أموكسيسيلين ٢٥٠ ملغ',    'points_cost' => 12, 'expiry_date' => '2027-01-20'],
            ['name' => 'أزيثروميسين ٢٥٠ ملغ',    'points_cost' => 15, 'expiry_date' => '2027-04-10'],
            ['name' => 'ميترونيدازول ٥٠٠ ملغ',    'points_cost' => 10, 'expiry_date' => '2027-09-01'],
            ['name' => 'أوميبرازول ٢٠ ملغ',       'points_cost' => 7,  'expiry_date' => '2027-05-15'],
            ['name' => 'سيتريزين ١٠ ملغ',         'points_cost' => 4,  'expiry_date' => '2027-08-25'],
            ['name' => 'ديكلوفيناك ٥٠ ملغ',       'points_cost' => 9,  'expiry_date' => '2027-02-28'],
            ['name' => 'أملوديبين ٥ ملغ',          'points_cost' => 6,  'expiry_date' => '2027-07-10'],
            ['name' => 'ميتفورمين ٨٥٠ ملغ',       'points_cost' => 11, 'expiry_date' => '2027-11-30'],
            ['name' => 'لوسارتان ٥٠ ملغ',         'points_cost' => 13, 'expiry_date' => '2027-10-20'],
            ['name' => 'سالبوتامول بخاخ',          'points_cost' => 20, 'expiry_date' => '2027-06-15'],
        ];

        foreach ($medicines as $medicine) {
            Medicine::firstOrCreate(
                ['name' => $medicine['name']],
                $medicine
            );
        }
    }
}
