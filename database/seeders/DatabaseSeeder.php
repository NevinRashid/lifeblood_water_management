<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\UsersAndTeams\Database\Seeders\PermissionSeeder;
use Modules\UsersAndTeams\Database\Seeders\RoleSeeder;
use Modules\UsersAndTeams\Database\Seeders\UsersAndTeamsDatabaseSeeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            UsersAndTeamsDatabaseSeeder::class
        ]);
    }
}
