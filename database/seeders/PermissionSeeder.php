<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'create water source',
            'view water source',
            'update water source',
            'delete water source',
            'attach documents to water source',
            'view water source map',
            'craete field team',
            'update field team',
            'view field team',
            'delete field team',
            'assign field team',
            
            'record water quantity',
            'view water quantity reports',
            'set water quality standards',
            'receive water level alerts',

            'record water quality analysis',
            'view water quality reports',
            'receive water quality alerts',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'sanctum',
            ]);
        }
    }
}
