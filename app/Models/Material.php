<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Material extends Model
{
    protected $fillable = ['dr_id', 'material_name', 'measurement', 'unit', 'material_quantity'];

    public function dr(): BelongsTo {
        return $this->belongsTo(Dr::class);
    }
}