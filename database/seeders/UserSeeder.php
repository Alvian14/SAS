<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => bcrypt('admin123'),
        ]);

        User::create([
            'email' => 'alvian@guru.com',
            'role' => 'teacher', // pastikan ini sesuai enum di migration
            'password' => bcrypt('alvianguru123'),
        ]);
    }
}
