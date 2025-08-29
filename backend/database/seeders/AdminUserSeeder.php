<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Editor User',
            'email' => 'editor@example.com',
            'password' => Hash::make('editor123'),
            'role' => 'editor',
            'email_verified_at' => now(),
        ]);

        $this->command->info('Admin and Editor users created successfully!');
        $this->command->info('Admin credentials: admin@example.com / admin123');
        $this->command->info('Editor credentials: editor@example.com / editor123');
    }
}