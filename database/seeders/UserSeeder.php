<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = User::create([
            'name'     => 'Super Admin',
            'phone'    => '0111111111',
            'address'  => 'address1',
            'email'    => 'superadmin@gmail.com',
            'password' => Hash::make('password123'),
        ]);
        $superAdmin->assignRole('Super Administrator');

        // مستخدم 2: National Water Authority Manager
        $authorityManager = User::create([
            'name'     => 'Authority Manager',
            'phone'    => '0111111111',
            'address'  => 'address1',
            'email'    => 'authorityManager@gmail.com',
            'password' => Hash::make('123456789'),
        ]);
        $authorityManager->assignRole('National Water Authority Manager');

        // مستخدم 3: Water Treatment Engineer
        $engineer = User::create([
            'name'     => 'Water Engineer',
            'phone'    => '0111111111',
            'address'  => 'address1',
            'email'    => 'engineer@gmail.com',
            'password' => Hash::make('123456789'),
        ]);
        $engineer->assignRole('Water Treatment Engineer');

        // مستخدم 4: Field Monitoring Team Member
        $fieldMonitor = User::create([
            'name'     => 'Field Team Member',
            'phone'    => '0111111111',
            'address'  => 'Field Base',
            'email'    => 'fieldMonitor@gmail.com',
            'password' => Hash::make('123456789'),
        ]);
        $fieldMonitor->assignRole('Field Monitoring');
    }
}
