<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Dr extends Model
{
    use HasFactory;

    protected $table = 'drs'; // Ensure the table name is correct


    public function materials(): HasMany {
        return $this->hasMany(Material::class);
    }

    protected $fillable = [
        'dr_number',            
        'name',                  
        'project_name',          
        'approved_by',          
        'status',               
        'materials',             // Ensure this is fillable
        'location',              
        'latitude',              
        'longitude',             
        'remarks',               
    ];

    protected $casts = [
        'materials' => 'array', // Ensures materials are stored/retrieved as an array
        'measurement_quantity' => 'integer', // Changed to integer
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

}