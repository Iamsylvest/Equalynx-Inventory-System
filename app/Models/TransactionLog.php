<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'action',
        'performed_by',
        'role',
        'timestamp',
        'transaction_details',
    ];

    protected $casts = [
        'transaction_details' => 'array', // Automatically cast the transaction details as an array
    ];
}
