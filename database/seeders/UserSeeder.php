<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Luis Danilo',
            'last_name' => 'Gomez Perez',
            'email' => 'perzluis8@gmail.com',
            'password' => Hash::make('admin'),
            'phone_number' => '1234567890',
            'document_type' => 'CC',
            'document_number' => '123456789',
            'is_enable' => true

        ]);
        $admin->assignRole('admin');

        $admin = User::create([
            'name' => 'Carla Maria',
            'last_name' => 'Moncada Ortiz',
            'email' => 'ingenieroortin2017@gmail.com',
            'password' => Hash::make('docente'),
            'phone_number' => '0987654321',
            'document_type' => 'CC',
            'document_number' => '987654321',
            'is_enable' => true
        ]);
        $admin->assignRole('docente');
    }
}
