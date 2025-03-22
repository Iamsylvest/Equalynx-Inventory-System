<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens; // Ensure HasApiTokens is included
    public $timestamps = true; // Make sure this line is present if timestamps are not working


    protected $appends = ['has_2fa']; // ✅ Automatically include it in API responses

    /**
     * Check if 2FA is enabled.
     */
    public function getHas2FAAttribute()
    {
        return !is_null($this->two_factor_code) && now()->lt($this->two_factor_expires_at);
    }

    /**
     * The attributes that are mass assignable.
     *  
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'is_active',
        'status',
        'email', // <-- Add email field
        'password',
        'role', // <-- Ensure role is included if used in seeder
        'phone_number', // Add phone number
        'two_factor_code', // Add OTP field
        'two_factor_expires_at', // Add OTP expiry time
        'is_two_factor_enabled',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_two_factor_enabled' => 'boolean' // ✅ Casts to boolean
    ];

    /**
     * Override the method that checks for user credentials using email.
     */
    public static function findForPassport($email)
    {
        return static::where('email', $email)->first();
    }
}