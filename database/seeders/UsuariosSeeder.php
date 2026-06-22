<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuariosSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate([
            'email' => 'admin@gmail.com',
        ], [
            'name' => 'Administrador Principal',
            'username' => 'admin',
            'role' => 'admin',
            'password' => Hash::make('12345678'),
        ]);

        User::updateOrCreate([
            'email' => 'organizador@gmail.com',
        ], [
            'name' => 'Usuario Organizador',
            'username' => 'organizador',
            'role' => 'organizer',
            'password' => Hash::make('12345678'),
        ]);
    }
}
