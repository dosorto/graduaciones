<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ValidadorSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate([
            'email' => 'validador@gmail.com',
        ], [
            'name' => 'Usuario Validador',
            'username' => 'validador',
            'role' => 'validator',
            'password' => Hash::make('12345678'),
        ]);
    }
}
