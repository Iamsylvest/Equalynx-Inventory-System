<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dr extends Model
{
    use SoftDeletes; // Enables soft deletes functionaility
    use HasFactory;

    protected $table = 'drs';     // ✅ Specify the table name to ensure Laravel maps it correctly

    /**
     * ✅ Relationship: One DR has many materials.
     * This means that a single delivery receipt (DR) can contain multiple materials.
     * The `dr_id` in the `materials` table acts as the foreign key linking to the `drs` table.
     */
    public function materials(): HasMany
    {
        return $this->hasMany(Material::class, 'dr_id');
    }


       /**
     * ✅ Relationship: A DR belongs to one approver (User).
     * This means that each DR is approved by one user, whose ID is stored in `approved_by`.
     * The `approved_by` column in the `drs` table acts as the foreign key linking to the `users` table.
     * Using this relationship, we can fetch details of the user who approved the DR.
     */
   public function approver(): BelongsTo
   {
    return $this->belongsTo(User::class, 'approved_by');
   }



 // ✅ Define the fillable attributes for mass assignment
    protected $fillable = [
        'dr_number',            
        'name',                   
        'project_name',          
        'approved_by',          
        'status',               
        'location',                          
        'remarks',    
        'created_at'           
    ];

  // ✅ Cast latitude and longitude to decimal format for precision
    protected $casts = [
        'approved_by' =>'integer', // ✅ Cast as integer
        'deleted_at' => 'datetime' // cast deleted_at to datetime for convenience
    ];



}