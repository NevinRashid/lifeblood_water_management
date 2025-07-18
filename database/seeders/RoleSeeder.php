<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = Role::create([
            'name' => 'Super Admin',
            'guard_name' => 'sanctum',
        ]);

        $authorityManager = Role::create([
            'name' => 'National Water Authority Manager',
            'guard_name' => 'sanctum',
        ]);

        $engineer = Role::create([
            'name' => 'Water Treatment Engineer',
            'guard_name' => 'sanctum',
        ]);

        $fieldTeam = Role::create([
            'name' => 'Field Monitoring ',
            'guard_name' => 'sanctum',
        ]);


        // Assign all permissions to Admin
        $superAdmin->syncPermissions(Permission::all());

        // National Water Authority Manager permissions
        $authorityManager->givePermissionTo([
            'create water source',
            'view water source',
            'update water source',
            'delete water source',
            'attach documents to water source',
            'view water source map',
            'set water quality standards',
            'view water quantity reports',
            'craete field team',
            'update field team',
            'view field team',
            'delete field team',
        ]);

        // Water Treatment Engineer permissions
        $engineer->givePermissionTo([
            'record water quality analysis',
            'view water quality reports',
            'receive water quality alerts',
            'view water source map',
            'view field team',
            'assign field team'
        ]);

        // Field Monitoring permissions
        $fieldTeam->givePermissionTo([
            'record water quantity',
            'receive water level alerts',
            'view water source map',
        ]);
    }

}
