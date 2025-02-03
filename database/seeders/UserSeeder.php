<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Make sure the User model is correctly imported
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon; // Import Carbon for working with dates


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         
        // Create the admin user
        $admin = User::create([
            'first_name' => 'Sylvest',
            'middle_name' => 'Falcutila',
            'last_name' => 'Madelo',
            'role' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('123456'),
            'is_active' => true,
            'created_at' => Carbon::now(), // Manually set created_at
            'updated_at' => Carbon::now(), // Manually set updated_at
        ]);

        // Create the manager (warehouse) user
        $manager = User::create([
            'first_name' => 'Geryk',
            'middle_name' => '',
            'last_name' => 'Bubutan',
            'role' => 'manager',
            'email' => 'manager@example.com',
            'password' => Hash::make('123456'),
            'is_active' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Create the manager (procurement) user
        $procurement = User::create([
            'first_name' => 'Princess',
            'middle_name' => '',
            'last_name' => 'Mamison',
            'role' => 'procurement',
            'email' => 'procurement@example.com',
            'password' => Hash::make('123456'),
            'is_active' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Create another user (warehouse staff)
        $warehouseStaff = User::create([
            'first_name' => 'Rachelle',
            'middle_name' => '',
            'last_name' => 'Regala',
            'role' => 'warehouse_staff',
            'email' => 'warehouse@example.com',
            'password' => Hash::make('123456'),
            'is_active' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Create another user (Johanna Maxine)
        $maxine = User::create([
            'first_name' => 'Johanna Maxine',
            'middle_name' => '',
            'last_name' => 'Dolz',
            'role' => 'admin',
            'email' => 'max@equalynx.com',
            'password' => Hash::make('123456'),
            'is_active' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
     
    }
}

