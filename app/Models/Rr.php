<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rr extends Model
{
    use SoftDeletes; // Enables soft deletes functionaility
    use HasFactory;

    protected $table = 'rrs'; // Ensure it matches your actual table name


    protected $fillable = [
        'dr_id',
        'rr_number',
        'name',
        'project_name',
        'approved_by',
        'status',
        'remarks',
        'return_proof',
        'return_proof_original_name',  // Add this
        'location',
        'latitude',
        'longitude',
    ];
    protected $attributes = [
        'approved_by' => null, // or 'System' if you want a default user
    ];

     // ✅ Relationship with ReturnMaterials
    public function materials() : HasMany 
    {
        return $this->hasMany(ReturnMaterial::class, 'rr_id');
    }

        // ✅ Relationship with DR Model
    public function dr(): BelongsTo
    {
        return $this->belongsTo(Dr::class, 'dr_id');
    }
    public function approver(): BelongsTo{
        return $this->belongsTo(User::class,'approved_by');
    }

}