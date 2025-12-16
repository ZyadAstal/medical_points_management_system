<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispense extends Model
{
    use HasFactory;

    protected $fillable = [
        'prescription_item_id',
        'medical_center_id',
        'quantity',
        'points_used'
    ];

    public function prescriptionItem()
    {
        return $this->belongsTo(PrescriptionItem::class);
    }

    public function medicalCenter()
    {
        return $this->belongsTo(MedicalCenter::class);
    }
}
