<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;


class Patient extends Model
{
    use HasFactory;

    protected $appends = ['name'];

    protected $fillable = [
        'user_id',
        'name', // Add name to fillable or map it
        'full_name',
        'national_id',
        'address',
        'phone',
        'points',
        'date_of_birth' // Add missing date_of_birth
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }


    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }
    public function getNameAttribute()
    {
        return $this->full_name;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['full_name'] = $value;
    }
}
