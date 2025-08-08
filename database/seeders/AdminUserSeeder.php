<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Check if admin user already exists
        $adminUser = User::where('email', 'admin@example.com')->first();
        
        if ($adminUser) {
            // Update existing user with proper password hash
            $adminUser->update([
                'password' => Hash::make('password'),
                'role' => 'admin'
            ]);
            echo "Admin user updated successfully!\n";
        } else {
            // Create new admin user
            User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin'
            ]);
            echo "Admin user created successfully!\n";
        }
    }
}
