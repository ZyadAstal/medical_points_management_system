<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Patient;
use App\Models\Visit;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Dispense;
use App\Models\Medicine;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $patientsData = [
            [
                'name'        => 'أحمد يوسف حسن النجار',
                'username'    => 'patient1',
                'email'       => 'ahmed.najjar@example.com',
                'national_id' => '401234567',
                'address'     => 'غزة - حي الشجاعية - شارع الوحدة',
                'phone'       => '0591234567',
                'points'      => 85,
                'date_of_birth' => '1990-03-15',
            ],
            [
                'name'        => 'سارة خالد إبراهيم أبو شمالة',
                'username'    => 'patient2',
                'email'       => 'sara.shamala@example.com',
                'national_id' => '403345678',
                'address'     => 'خان يونس - حي الأمل - بجوار مسجد الفاروق',
                'phone'       => '0592345678',
                'points'      => 60,
                'date_of_birth' => '1985-07-22',
            ],
            [
                'name'        => 'محمود عبد الله ناصر الفرا',
                'username'    => 'patient3',
                'email'       => 'mahmoud.farra@example.com',
                'national_id' => '405678901',
                'address'     => 'رفح - حي تل السلطان - شارع المحطة',
                'phone'       => '0593456789',
                'points'      => 40,
                'date_of_birth' => '1978-11-08',
            ],
            [
                'name'        => 'فاطمة محمود علي الأسطل',
                'username'    => 'patient4',
                'email'       => 'fatima.astal@example.com',
                'national_id' => '403456789',
                'address'     => 'دير البلح - المعسكر الجديد - شارع صلاح الدين',
                'phone'       => '0594567890',
                'points'      => 100,
                'date_of_birth' => '1995-01-30',
            ],
            [
                'name'        => 'محمد أحمد شعبان أبو هاشم',
                'username'    => 'patient5',
                'email'       => 'mohammed.hashem@example.com',
                'national_id' => '405678903',
                'address'     => 'غزة - حي الرمال - شارع عمر المختار',
                'phone'       => '0595678901',
                'points'      => 95,
                'date_of_birth' => '1988-06-12',
            ],
            [
                'name'        => 'آلاء سامي خليل عوض',
                'username'    => 'patient6',
                'email'       => 'alaa.awad@example.com',
                'national_id' => '407788990',
                'address'     => 'بيت لاهيا - شمال غزة - حي السلام',
                'phone'       => '0596789012',
                'points'      => 30,
                'date_of_birth' => '1992-09-05',
            ],
            [
                'name'        => 'خالد عمر ناصر الدحدوح',
                'username'    => 'patient7',
                'email'       => 'khaled.dahdouh@example.com',
                'national_id' => '402233445',
                'address'     => 'جباليا - مخيم جباليا - بجوار وكالة الغوث',
                'phone'       => '0597890123',
                'points'      => 70,
                'date_of_birth' => '1982-04-18',
            ],
            [
                'name'        => 'هدى محمود سالم المصري',
                'username'    => 'patient8',
                'email'       => 'huda.masri@example.com',
                'national_id' => '401199001',
                'address'     => 'النصيرات - المنطقة الوسطى - شارع البحر',
                'phone'       => '0598901234',
                'points'      => 15,
                'date_of_birth' => '1998-12-25',
            ],
            [
                'name'        => 'رامي إياد حسن شراب',
                'username'    => 'patient9',
                'email'       => 'rami.sharab@example.com',
                'national_id' => '406655443',
                'address'     => 'خان يونس - حي المنارة - قرب الجامع الكبير',
                'phone'       => '0599012345',
                'points'      => 55,
                'date_of_birth' => '1975-08-03',
            ],
            [
                'name'        => 'سلمى نادر علي حمد',
                'username'    => 'patient10',
                'email'       => 'salma.hamad@example.com',
                'national_id' => '403030301',
                'address'     => 'غزة - حي الزيتون - شارع الجلاء',
                'phone'       => '0590123456',
                'points'      => 48,
                'date_of_birth' => '2000-02-14',
            ],
            [
                'name'        => 'نور محمد عادل أبو سمرة',
                'username'    => 'patient11',
                'email'       => 'nour.samra@example.com',
                'national_id' => '409988776',
                'address'     => 'رفح - حي الجنينة - شارع النصر',
                'phone'       => '0591122334',
                'points'      => 90,
                'date_of_birth' => '1993-05-20',
            ],
            [
                'name'        => 'يوسف طارق عبد الكريم سكيك',
                'username'    => 'patient12',
                'email'       => 'yousef.skik@example.com',
                'national_id' => '408877665',
                'address'     => 'بيت حانون - شمال قطاع غزة - الشارع الرئيسي',
                'phone'       => '0592233445',
                'points'      => 20,
                'date_of_birth' => '1987-10-11',
            ],
            [
                'name'        => 'ليلى أحمد حمد العطار',
                'username'    => 'patient13',
                'email'       => 'layla.attar@example.com',
                'national_id' => '402323234',
                'address'     => 'غزة - حي النصر - بجوار مستشفى الشفاء',
                'phone'       => '0593344556',
                'points'      => 78,
                'date_of_birth' => '1996-03-28',
            ],
            [
                'name'        => 'إيهاب سامر سالم أبو ريدة',
                'username'    => 'patient14',
                'email'       => 'ehab.rida@example.com',
                'national_id' => '400101012',
                'address'     => 'المغازي - المنطقة الوسطى - شارع الشهداء',
                'phone'       => '0594455667',
                'points'      => 92,
                'date_of_birth' => '1980-07-07',
            ],
            [
                'name'        => 'دعاء ناصر محمود الزعانين',
                'username'    => 'patient15',
                'email'       => 'duaa.zaanin@example.com',
                'national_id' => '404433221',
                'address'     => 'بيت لاهيا - شمال غزة - حي العزبة',
                'phone'       => '0595566778',
                'points'      => 35,
                'date_of_birth' => '1991-11-19',
            ],
        ];

        // Get all doctors
        $doctorRoleId = Role::where('name', 'Doctor')->first()?->id ?? 3;
        $doctors = User::where('role_id', $doctorRoleId)->get();
        $today = Carbon::today();

        foreach ($patientsData as $index => $data) {
            // Create user for patient
            $user = User::firstOrCreate(
                ['username' => $data['username']],
                [
                    'name'              => $data['name'],
                    'username'          => $data['username'],
                    'email'             => $data['email'],
                    'password'          => Hash::make('password'),
                    'role_id'           => 5, // Patient
                    'medical_center_id' => ($index % 4) + 1, // distribute across centers
                ]
            );

            // Create patient record
            $patient = Patient::firstOrCreate(
                ['national_id' => $data['national_id']],
                [
                    'user_id'     => $user->id,
                    'full_name'   => $data['name'],
                    'national_id' => $data['national_id'],
                    'address'     => $data['address'],
                    'phone'       => $data['phone'],
                    'points'      => min($data['points'] ?? 100, 100), // Max 100 points as requested
                    'date_of_birth' => $data['date_of_birth'],
                ]
            );

            if ($doctors->isEmpty()) continue;

            $doctor = $doctors[$index % $doctors->count()];
            $centerId = $doctor->medical_center_id;

            // --- Create Visits (today + past) ---

            // Today's visit
            if ($index < 8) {
                Visit::create([
                    'patient_id'       => $patient->id,
                    'doctor_id'        => $doctor->id,
                    'medical_center_id' => $centerId,
                    'status'           => $index < 3 ? Visit::STATUS_COMPLETED : Visit::STATUS_WAITING,
                    'priority'         => $index < 3 ? Visit::PRIORITY_EMERGENCY : Visit::PRIORITY_NORMAL,
                    'visit_date'       => $today->toDateString(),
                    'notes'            => 'زيارة مراجعة دورية',
                ]);
            }

            // Past visits
            $pastVisitCount = ($index % 3) + 1;
            for ($v = 1; $v <= $pastVisitCount; $v++) {
                Visit::create([
                    'patient_id'       => $patient->id,
                    'doctor_id'        => $doctor->id,
                    'medical_center_id' => $centerId,
                    'status'           => Visit::STATUS_COMPLETED,
                    'priority'         => Visit::PRIORITY_NORMAL,
                    'visit_date'       => $today->copy()->subDays($v * 7 + $index)->toDateString(),
                    'notes'            => 'زيارة سابقة رقم ' . $v,
                ]);
            }
        }
    }
}
