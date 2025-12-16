<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'points_cost', 'expiry_date'];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    public function prescriptionItems()
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function dispenses()
    {
        return $this->hasMany(Dispense::class);
    }
}
