<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;
    protected $table = 'unit';
    protected $fillable = [
        'name',
        'image',
        'unit_type_id',
        'description',
        'size',
        'city',
        'province',
        'address',
    ];

    public function getImageAttribute($value)
    {
        // Remove any leading and trailing quotes if present
        $value = trim($value, '"');

        // Decode the JSON string
        $image = json_decode($value, true);

        // If JSON decode fails, try decoding again after stripping slashes
        if (json_last_error() !== JSON_ERROR_NONE) {
            $image = json_decode(stripslashes($value), true);
        }

        // If it is still not valid JSON, return an empty array
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($image)) {
            return [];
        }

        return $image;
        }


    // Define the relationship with UnitType
    public function unitType()
    {
        return $this->belongsTo(UnitType::class, 'unit_type_id');
    }
}
