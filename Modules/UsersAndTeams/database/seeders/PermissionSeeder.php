<?php

namespace Modules\UsersAndTeams\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

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

            'view water quantity reports',
            'set water quality standards',

            'record water quality analysis',
            'view water quality reports',
            'receive water quality alerts',

            'view_network_map',
            'update_network_component',
            'create_fault_report',
            'view_fault_reports',
            'assign_repair_team',
            'update_repair_status',
            'log_repair_costs',
            'delete_fault_report',
            'view_sensor_data',
            'receive_fault_alerts',
            'receive_pressure_alerts',
            'receive_repair_task_alerts',
            'view_efficiency_reports',
            'view_repair_reports',

            'view_reservoir_levels',
            'create_fill_operation',
            'create_empty_operation',
            'update_reservoir_status',
            'delete_reservoir_record',
            'view_tanker_fleet',
            'create_tanker',
            'update_tanker',
            'delete_tanker',
            'plan_tanker_routes',
            'update_tanker_routes',
            'view_distribution_records',
            'create_distribution_record',
            'update_distribution_record',
            'receive_reservoir_alerts',

            'create_water_quality_test',
            'update_water_quality_test',
            'create_sensor_reading',
            'update_sensor_reading',

            'create_water_extraction',
            'update_water_extraction',
            'delete_water_extraction',
            'view_water_extraction',

            'create_infrastructure_status',
            'update_infrastructure_status',
            'view_distribution_network_map',
            'create_field_report',
            'upload_field_photos',

            'create_trouble_ticket',
            'view_own_tickets',
            'receive_notifications',
            'submit_feedback',
            'view_public_water_status',
            'view_distribution_point_info',

            'view_donor_dashboard',
            'access_funding_reports',
            'download_beneﬁciary_stats',
            'request_custom_reports',

            'generate_water_quality_reports',
            'predict_water_shortages',
            'access_environmental_impact_data',

            'show_logs',

            'create_beneficiary',
            'update_beneficiary',
            'delete_beneficiary',
            'view_beneficiary',

            'create_water_quota',
            'update_water_quota',
            'delete_water_quota',
            'view_water_quota',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'sanctum'
            ]);
        }
    }
}
