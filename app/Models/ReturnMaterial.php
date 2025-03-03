<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnMaterial extends Model
{
    use HasFactory;

    protected $fillable = ['rr_id', 'material_name', 'measurement', 'unit', 'material_quantity'];

    public function rr(): BelongsTo {
        return $this->belongsTo(Rr::class, 'rr_id');
    }
}
