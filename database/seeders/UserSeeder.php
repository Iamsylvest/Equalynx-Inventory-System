<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Make sure the User model is correctly imported
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Test User',
            'email' => 'testuser@example.com', // Provide an email here
            'username' => 'testuser',           // Provide a username
            'password' => Hash::make('password123'), // Make sure the password is hashed
        ]);
    }
}
