<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'medical_center_id',
        'medicine_id',
        'quantity'
    ];

    public function medicalCenter()
    {
        return $this->belongsTo(MedicalCenter::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
}
