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
            'guard_name' => 'sanctum'
        ]);

        $authorityManager = Role::create([
            'name' => 'National Water Authority Manager',
            'guard_name' => 'sanctum'

        ]);

        $engineer = Role::create([
            'name' => 'Treatment Plant Engineer',
            'guard_name' => 'sanctum'


        ]);

        $networkManager = Role::create([
            'name' => 'Distribution Network Manager',
            'guard_name' => 'sanctum'


        ]);

        $reservoirAndTankerSupervisor = Role::create([
            'name' => 'Reservoir And Tanker Supervisor',
            'guard_name' => 'sanctum'


        ]);

        $fieldMonitoringAgent = Role::create([
            'name' => 'Field Monitoring Agent',
            'guard_name' => 'sanctum'

        ]);

        $affectedmember = Role::create([
            'name' => 'Affected Community Member',
            'guard_name' => 'sanctum'

        ]);

        $donorAgency = Role::create([
            'name' => 'Donor Agency',
            'guard_name' => 'sanctum'

        ]);

        $dataAnalyst = Role::create([
            'name' => 'Enviromental Data Analyst',
            'guard_name' => 'sanctum'

        ]);

        // Assign all permissions to Admin
        $superAdmin->syncPermissions(Permission::all());

        // National Water Authority Manager permissions
        $authorityManager->givePermissionTo([
            'create_water_source',
            'view_water_source',
            'update_water_source',
            'delete_water_source',
            'attach_documents_to_water_source',
            'view_water_source_map',
            'craete_field_team',
            'update_field_team',
            'view_field_team',
            'delete_field_team',
            'view_water_quantity_reports',
            'set_water_quality_standards',
            'view_risk_heatmap',
            'create water source',
            'view water source',
            'update water source',
            'delete water source',
            'attach documents to water source',
            'view water source map',
            'set water quality standards',
            'view water quantity reports',
            'destroy water quality analysis',
            'craete field team',
            'update field team',
            'view field team',
            'delete field team',
            'create_beneficiary',
            'update_beneficiary',
            'delete_beneficiary',
            'view_beneficiary',
            'view_any_beneficiaries',
            'view water source parameters',
            'assign water source parameters',
            'unassign water source parameters',
            'view testing parameters',
            'view_network_reports',
        ]);

        // Water Treatment Engineer permissions
        $engineer->givePermissionTo([
            'record_water_quality_analysis',
            'view_water_quality_reports',
            'receive_water_quality_alerts',
            'view_water_source_map',
            'view_field_team',
            'assign_field_team',
            'view_risk_heatmap',
            'create_water_extraction',
            'update_water_extraction',
            'delete_water_extraction',
            'view_water_extraction',
            'view_any_water_extraction',
            'record water quality analysis',
            'view water quality reports',
            'destroy water quality analysis',
            'receive water quality alerts',
            'view water source map',
            'view field team',
            'assign field team',
            'view water source parameters',
            'view testing parameters',
            'create testing parameter',
            'update testing parameter',
            'delete testing parameter',
        ]);

        // Network Distribution / Maintenance Manager Permissions
        $networkManager->givePermissionTo([
            'view_distribution_network_map',
            'create_distribution_network',
            'update_distribution_network',
            'show_distribution_network',
            'delete_distribution_network',
            'create_distribution_network_component',
            'update_distribution_network_component',
            'view_all_distribution_network_components',
            'show_distribution_network_component',
            'delete_distribution_network_component',
            'view_all_distribution_network_component',
            'view_sensor_data',
            'receive_pressure_alerts',
            'create_trouble_ticket',
            'update_trouble_ticket',
            'show_trouble_ticket',
            'view_all_trouble_tickets',
            'delete_trouble_ticket',
            'approve_trouble_ticket',
            'reject_trouble_ticket',
            'review_complaint',
            'change_trouble_ticket_status',
            'view_citizen_trouble_tickets',
            'view_citizen_complaints',
            'create_reform',
            'update_reform',
            'show_reform',
            'view_all_reforms',
            'delete_reform',
            'upload_reform_images',
            'create_team',
            'update_team',
            'show_team',
            'view_all_teams',
            'delete_team',
            'assign_members_for_team',
            'remove_members_form_team',
            'receive_repair_task_alerts',
            'view_efficiency_reports',
            'view_repair_reports',
            'create_water_quota',
            'update_water_quota',
            'delete_water_quota',
            'view_water_quota',
            'view_any_water_quota',
            'show_reservoir_activity',
            'view_all_reservoirs_activity',
            'get_reservoir_current_level',
            'view_network_reports',
        ]);

        // Water Reservoir / Tanker Officer Permissions
        $reservoirAndTankerSupervisor->givePermissionTo([
            'view_distribution_network_map',
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
            'create_reservoir_activity',
            'show_reservoir_activity',
            'view_all_reservoirs_activity',
            'update_reservoir_activity',
            'delete_reservoir_activity',
            'get_reservoir_current_level',
            'view tanker routes',
            'delete distribution record',
            'assign user to tanker',
            'unassign user from tanker',
            'view tanker assignments',
        ]);

        // Field Monitoring permissions
        $fieldMonitoringAgent->givePermissionTo([
            'create_water_quality_test',
            'update_water_quality_test',
            'destroy water quality analysis',
            'create_sensor_reading',
            'update_sensor_reading',
            'create_infrastructure_status',
            'update_infrastructure_status',
            'view_distribution_network_map',
            'create_field_report',
            'upload_field_photos',
            'create_trouble_ticket',
            'update_trouble_ticket',
            'show_trouble_ticket',
            'delete_trouble_ticket',
            'upload_reform_images',
            'view water source parameters',
            'view testing parameters',
        ]);

        // Affected Community Member permissions
        $affectedmember->givePermissionTo([
            'create_trouble_ticket',
            'show_trouble_ticket',
            'delete_trouble_ticket',
            'view_citizen_trouble_tickets',
            'view_citizen_complaints',
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
            'access_environmental_impact_data',
            'view water source parameters',
            'view testing parameters',
        ]);
    }
}
