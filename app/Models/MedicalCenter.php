<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalCenter extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'location', 'phone'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    public function dispenses()
    {
        return $this->hasMany(Dispense::class);
    }
}
