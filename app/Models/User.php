<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Traits\SearchableArabic;

class User extends Authenticatable
{
    use HasFactory, SearchableArabic;

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

    public function doctorVisits()
    {
        return $this->hasMany(Visit::class, 'doctor_id');
    }

    public function patient()
    {
        return $this->hasOne(Patient::class);
    }

    public function prescriptionsAsDoctor()
    {
        return $this->hasMany(Prescription::class, 'doctor_id');
    }
}
