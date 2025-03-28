<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear 4 usuarios regulares
        $users = [
            [
                'nombre' => 'aaa',
                'apellido' => 'aaa',
                'email' => 'aaa@gmail.com',
                'telefono' => '612345678',
                'password' => Hash::make('1234'),
                'rol' => 'user',
            ],
            [
                'nombre' => 'purvishh',
                'apellido' => 'patel',
                'email' => 'a23purpatmah@inspedralbes.cat',
                'telefono' => '623456789',
                'password' => Hash::make('12345678'),
                'rol' => 'user',
            ],
            [
                'nombre' => 'Carlos',
                'apellido' => 'Rodríguez',
                'email' => 'carlos@gmail.com',
                'telefono' => '634567890',
                'password' => Hash::make('12345678'),
                'rol' => 'user',
            ],
            [
                'nombre' => 'Ana',
                'apellido' => 'Martínez',
                'email' => 'ana@gmail.com',
                'telefono' => '645678901',
                'password' => Hash::make('12345678'),
                'rol' => 'user',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}