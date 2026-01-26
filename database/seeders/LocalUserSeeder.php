<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class LocalUserSeeder extends Seeder
{
    public function run()
    {
        $user = User::updateOrCreate(
            ['email' => 'admin@pmis.com'],
            [
                'hris_id' => null,
                'name' => 'PMIS Admin',
                'password' => bcrypt('#3ncrypt3D#'),
            ]
        );

        $user->assignRole('super_admin');
    }
}
