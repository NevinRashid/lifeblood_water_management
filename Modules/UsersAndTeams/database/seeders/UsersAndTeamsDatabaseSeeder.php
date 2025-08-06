<?php

namespace Modules\UsersAndTeams\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\UsersAndTeams\Models\User;

class UsersAndTeamsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = User::firstOrCreate([
            'name'     => 'Rama',
            'email'    => 'ramaabdo657@gmail.com',
            'password' => '123456789',
        ]);
        $superAdmin->assignRole('Super Admin');

        //National Water Authority Manager
        $authorityManager = User::firstOrCreate([
            'name'     => 'Rama',
            'email'    => 'authorityManager@gmail.com',
            'password' => '123456789',
        ]);
        $authorityManager->assignRole('National Water Authority Manager');

        //Water Treatment Engineer
        $engineer = User::firstOrCreate([
            'name'     => 'Haneen',
            'email'    => 'engineer@gmail.com',
            'password' => '123456789'
        ]);
        $engineer->assignRole('Treatment Plant Engineer');

        //Distribution Network Manager
        $networkManager = User::firstOrCreate([
            'name'     => 'Nevin',
            'email'    => 'fieldMonitor@gmail.com',
            'password' => '123456789'
        ]);
        $networkManager->assignRole('Distribution Network Manager');

        //Reservoir And Tanker Supervisor
        $reservoirAndTankerSupervisor = User::firstOrCreate([
            'name'     => 'Moneer',
            'email'    => 'reservoirAndTankerSupervisor@gmail.com',
            'password' => '123456789'
        ]);
        $reservoirAndTankerSupervisor->assignRole('Reservoir And Tanker Supervisor');

        //Field Monitoring Agent
        $fieldMonitoringAgent = User::firstOrCreate([
            'name'     => 'Mohamad',
            'email'    => 'fieldMonitoringAgent@gmail.com',
            'password' => '123456789'
        ]);
        $fieldMonitoringAgent->assignRole('Field Monitoring Agent');

        //Affected Community Member
        $affectedmember = User::firstOrCreate([
            'name'     => 'Ziad',
            'email'    => 'affectedmember@gmail.com',
            'password' => '123456789'
        ]);
        $affectedmember->assignRole('Affected Community Member');

        //Donor Agency
        $donorAgency = User::firstOrCreate([
            'name'     => 'Omar',
            'email'    => 'donorAgency@gmail.com',
            'password' => '123456789'
        ]);
        $donorAgency->assignRole('Donor Agency');

        //Enviromental Data Analyst
        $dataAnalyst = User::firstOrCreate([
            'name'     => 'lara',
            'email'    => 'dataAnalyst@gmail.com',
            'password' => '123456789'
        ]);
        $dataAnalyst->assignRole('Enviromental Data Analyst');
    }
}
