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
        // Create the admin user and generate token
        $admin = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);
      


        // Create the manager (warehouse) user and generate token
        $manager = User::create([
            'name' => 'Test Manager',
            'email' => 'warehouse@example.com',
            'password' => Hash::make('password123'),
            'role' => 'manager',
        ]);



        // Create the manager (procurement) user and generate token
        $procurement = User::create([
            'name' => 'Test Procurement',
            'email' => 'procurement@example.com',
            'password' => Hash::make('password123'),
            'role' => 'procurement',
        ]);



        // Create another user (warehouse staff) and generate token
        $warehouseStaff = User::create([
            'name' => 'Test Warehouse Staff',
            'email' => 'warehouse_staff@example.com',
            'password' => Hash::make('password123'),
            'role' => 'warehousestaff',
        ]);
       
     
    }
}

