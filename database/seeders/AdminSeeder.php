<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user if it doesn't exist
        User::firstOrCreate(
            ['email' => 'moviebloggroup3@gmail.com'],
            [
                'name' => 'MovieBlog Admin',
                'password' => Hash::make('Movie@123'),
                'role' => 'admin',
                'email_verified_at' => now(), // Pre-verify for demo
            ]
        );

        // Create regular user if it doesn't exist
        User::firstOrCreate(
            ['email' => 'user@movieblog.com'],
            [
                'name' => 'Regular User',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'email_verified_at' => now(), // Pre-verify for demo
            ]
        );
    }
}
