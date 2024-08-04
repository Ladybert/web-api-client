<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    // Define the relationship with ResidentialEstate
    public function residentialEstates()
    {
        return $this->hasMany(ResidentialEstate::class, 'unit_type_id');
    }
}
