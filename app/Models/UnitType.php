<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitType extends Model
{
    use HasFactory;

    protected $table = 'unit_type';

    protected $fillable = [
        'name'
    ];

    // Define the relationship with ResidentialEstate
    public function Unit()
    {
        return $this->hasMany(Unit::class, 'unit_type_id');
    }
}
