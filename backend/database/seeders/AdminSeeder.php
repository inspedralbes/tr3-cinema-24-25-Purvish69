<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear 2 administradores
        $admins = [
            [
                'nombre' => 'Purvish',
                'apellido' => 'patel',
                'email' => 'purvish@filmgalaxy.com',
                'telefono' => '600000001',
                'password' => Hash::make('1234'),
                'rol' => 'admin',
            ],
            [
                'nombre' => 'bbb',
                'apellido' => 'bbb',
                'email' => 'bbb@filmgalaxy.com',
                'telefono' => '600000002',
                'password' => Hash::make('1234'),
                'rol' => 'admin',
            ],
        ];

        foreach ($admins as $admin) {
            User::create($admin);
        }
    }
}