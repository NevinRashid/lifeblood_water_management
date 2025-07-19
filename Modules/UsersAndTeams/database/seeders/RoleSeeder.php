<?php

namespace Modules\UsersAndTeams\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = Role::create([
            'name' => 'Super Admin',
        ]);

        $authorityManager = Role::create([
            'name' => 'National Water Authority Manager',
        ]);

        $engineer = Role::create([
            'name' => 'Treatment Plant Engineer',
        ]);

        $networkManager = Role::create([
            'name' => 'Distribution Network Manager',
        ]);

        $reservoirAndTankerSupervisor = Role::create([
            'name' => 'Reservoir And Tanker Supervisor',
        ]);

        $fieldMonitoringAgent = Role::create([
            'name' => 'Field Monitoring Agent',
        ]);

        $affectedmember = Role::create([
            'name' => 'Affected Community Member',
        ]);

        $donorAgency = Role::create([
            'name' => 'Donor Agency',
        ]);

        $dataAnalyst = Role::create([
            'name' => 'Enviromental Data Analyst',
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

        // Network Distribution / Maintenance Manager Permissions
        $networkManager->givePermissionTo([
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
        ]);

        // Water Reservoir / Tanker Officer Permissions
        $reservoirAndTankerSupervisor->givePermissionTo([
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
            'receive_reservoir_alerts'
        ]);

        // Field Monitoring permissions
        $fieldMonitoringAgent->givePermissionTo([
            'create_water_quality_test',
            'update_water_quality_test',
            'create_sensor_reading',
            'update_sensor_reading',
            'create_water_extraction',
            'update_water_extraction',
            'create_infrastructure_status',
            'update_infrastructure_status',
            'view_distribution_network_map',
            'create_field_report',
            'upload_field_photos',
        ]);

        // Affected Community Member permissions
        $affectedmember->givePermissionTo([
            'create_trouble_ticket',
            'view_own_tickets',
            'receive_notifications',
            'submit_feedback',
            'view_public_water_status',
            'view_distribution_point_info'
        ]);

        // Donor Agency Permissions
        $donorAgency->givePermissionTo([
            'view_donor_dashboard',
            'access_funding_reports',
            'download_beneﬁciary_stats',
            'request_custom_reports'
        ]);


        // Data Analyst Permissions
        $dataAnalyst->givePermissionTo([
            'generate_water_quality_reports',
            'predict_water_shortages',
            'access_environmental_impact_data'
        ]);

}
}
