<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    const STATUS_WAITING = 'waiting';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    const PRIORITY_NORMAL = 0;
    const PRIORITY_EMERGENCY = 1;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'medical_center_id',
        'status',
        'priority',
        'visit_date',
        'notes',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function medicalCenter()
    {
        return $this->belongsTo(MedicalCenter::class);
    }
}
