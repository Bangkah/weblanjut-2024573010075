<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Adi Siauw',
            'email' => 'adi@ilmudata.id',
            'password' => Hash::make('password'),
        ]);

        // Atau buat multiple users
        User::factory()->count(10)->create();
    }
}
