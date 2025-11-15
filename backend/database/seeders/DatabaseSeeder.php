<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a default admin user
        \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@school.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);

        $this->command->info('Created admin user (email: admin@school.com, password: password)');

        // Seed students
        $this->call([
            StudentSeeder::class,
        ]);

        // Seed attendance records
        $this->call([
            AttendanceSeeder::class,
        ]);
    }
}
