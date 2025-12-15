<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    public function prescriptionItems()
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
}
