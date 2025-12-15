<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function medicalCenter()
    {
        return $this->belongsTo(MedicalCenter::class);
    }

    public function patientProfile()
    {
        return $this->hasOne(Patient::class);
    }

    public function prescriptionsAsDoctor()
    {
        return $this->hasMany(Prescription::class, 'doctor_id');
    }
}
