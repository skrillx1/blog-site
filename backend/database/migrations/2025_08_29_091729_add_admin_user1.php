<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up()
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin2@example.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create editor user
        User::create([
            'name' => 'Editor User',
            'email' => 'editor2@example.com',
            'password' => Hash::make('editor123'),
            'role' => 'editor',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        // Remove the users if migration is rolled back
        User::where('email', 'admin@example.com')->delete();
        User::where('email', 'editor@example.com')->delete();
    }
};