<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;


    // Define the table name (optional, if it differs from the default 'inventory')
    protected $table = 'inventory';


     // Define the fillable fields to allow mass assignment
    protected $fillable = [
        'material_name',
        'stocks',
        'measurement_quantity',
        'measurement_unit',
    ];

}
