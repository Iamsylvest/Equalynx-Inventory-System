<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dr extends Model
{
    use HasFactory;

    protected $table = 'drs'; // Ensure the table name is correct
    public function materials(): HasMany
    {
        return $this->hasMany(Material::class, 'dr_id');
    }
    protected $fillable = [
        'dr_number',            
        'name',                   
        'project_name',          
        'approved_by',          
        'status',               
        'location',              
        'latitude',              
        'longitude',             
        'remarks',    
        'created_at'           
    ];

    protected $casts = [
        'latitude' => 'decimal:12',
        'longitude' => 'decimal:12',
    ];
}