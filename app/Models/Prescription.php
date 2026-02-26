<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    protected $appends = ['status'];

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'notes',
        'issued_at'
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    public function getStatusAttribute()
    {
        $items = $this->items;
        if ($items->isEmpty()) return 'لا يوجد أدوية';
        
        $totalCount = $items->count();
        $dispensedCount = $items->where('is_dispensed', true)->count();
        
        if ($dispensedCount == $totalCount) return 'تم الصرف';
        if ($dispensedCount == 0) return 'لم يتم الصرف';
        return 'صرف جزئي';
    }

    public function getStatusColorAttribute()
    {
        $status = $this->status;
        if ($status == 'تم الصرف') return 'success';
        if ($status == 'لم يتم الصرف') return 'danger';
        return 'warning';
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function items()
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    public function dispenses()
    {
        return $this->hasManyThrough(Dispense::class, PrescriptionItem::class);
    }
}
