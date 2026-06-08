<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        User::create([
            'Username' => 'Shirasu Azusa',
            'Password' => Hash::make('12345678'),
            'Email' => 'shirasuazusa@gmail.com',
            'NamaLengkap' => 'Shirasu Azusa',
            'Alamat' => 'Indonesia',
        ]);
    }
}
