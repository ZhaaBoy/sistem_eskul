<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate([
            'email' => 'admin@smkyappika.test',
        ], [
            'name' => 'Admin SMK Yappika',
            'password' => 'password',
            'role' => 'admin',
            'status' => 'aktif',
        ]);
    }
}
