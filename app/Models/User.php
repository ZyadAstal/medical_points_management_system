<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role_id',
        'medical_center_id'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

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
