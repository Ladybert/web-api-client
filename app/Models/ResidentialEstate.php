<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResidentialEstate extends Model
{
    use HasFactory;

    protected $fillable = [
        'housing_name',
        'image',
        'unit_type_id',
        'description',
        'size',
        'location',
    ];

    // Define the relationship with UnitType
    public function unitType()
    {
        return $this->belongsTo(UnitType::class, 'unit_type_id');
    }
}
