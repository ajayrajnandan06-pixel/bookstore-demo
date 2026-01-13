<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@bookstore.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'),
            'remember_token' => Str::random(10),
        ]);

        // Create Regular User (Optional)
        User::create([
            'name' => 'Manager',
            'email' => 'manager@bookstore.com',
            'email_verified_at' => now(),
            'password' => Hash::make('manager123'),
            'remember_token' => Str::random(10),
        ]);

        $this->command->info('Admin users created successfully!');
        $this->command->info('Email: admin@bookstore.com | Password: admin123');
        $this->command->info('Email: manager@bookstore.com | Password: manager123');
    }
}