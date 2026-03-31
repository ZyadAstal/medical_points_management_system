<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medicine;

class MedicineSeeder extends Seeder
{
    public function run(): void
    {
        $medicines = [
            ['name' => 'باراسيتامول ٥٠٠ ملغ',   'name_en' => 'Paracetamol 500mg',    'points_cost' => 5,  'expiry_date' => '2027-06-30'],
            ['name' => 'إيبوبروفين ٤٠٠ ملغ',     'name_en' => 'Ibuprofen 400mg',      'points_cost' => 8,  'expiry_date' => '2027-03-15'],
            ['name' => 'أموكسيسيلين ٢٥٠ ملغ',    'name_en' => 'Amoxicillin 250mg',    'points_cost' => 12, 'expiry_date' => '2027-01-20'],
            ['name' => 'أزيثروميسين ٢٥٠ ملغ',    'name_en' => 'Azithromycin 250mg',   'points_cost' => 15, 'expiry_date' => '2027-04-10'],
            ['name' => 'ميترونيدازول ٥٠٠ ملغ',    'name_en' => 'Metronidazole 500mg',  'points_cost' => 10, 'expiry_date' => '2027-09-01'],
            ['name' => 'أوميبرازول ٢٠ ملغ',       'name_en' => 'Omeprazole 20mg',      'points_cost' => 7,  'expiry_date' => '2027-05-15'],
            ['name' => 'سيتريزين ١٠ ملغ',         'name_en' => 'Cetirizine 10mg',      'points_cost' => 4,  'expiry_date' => '2027-08-25'],
            ['name' => 'ديكلوفيناك ٥٠ ملغ',       'name_en' => 'Diclofenac 50mg',      'points_cost' => 9,  'expiry_date' => '2027-02-28'],
            ['name' => 'أملوديبين ٥ ملغ',          'name_en' => 'Amlodipine 5mg',       'points_cost' => 6,  'expiry_date' => '2027-07-10'],
            ['name' => 'ميتفورمين ٨٥٠ ملغ',       'name_en' => 'Metformin 850mg',      'points_cost' => 11, 'expiry_date' => '2027-11-30'],
            ['name' => 'لوسارتان ٥٠ ملغ',         'name_en' => 'Losartan 50mg',        'points_cost' => 13, 'expiry_date' => '2027-10-20'],
            ['name' => 'سالبوتامول بخاخ',          'name_en' => 'Salbutamol Inhaler',   'points_cost' => 20, 'expiry_date' => '2027-06-15'],
        ];

        foreach ($medicines as $medicine) {
            Medicine::firstOrCreate(
                ['name' => $medicine['name']],
                $medicine
            );
        }
    }
}
