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
            'assign_field_team',

            'view_water_quantity_reports',
            'set_water_quality_standards',

            'record_water_quality_analysis',
            'view_water_quality_reports',
            'receive_water_quality_alerts',

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
            'view_any_water_extraction',

            'create_infrastructure_status',
            'update_infrastructure_status',
            'create_field_report',
            'upload_field_photos',


            'view_public_water_status',
            'view_distribution_point_info',

            'view_donor_dashboard',
            'access_funding_reports',
            'download_beneﬁciary_stats',
            'request_custom_reports',

            'generate_water_quality_reports',
            'predict_water_shortages',
            'access_environmental_impact_data',

            'view_risk_heatmap',

            'show_logs',

            'create_beneficiary',
            'update_beneficiary',
            'delete_beneficiary',
            'view_beneficiary',
            'view_any_beneficiaries',

            'create_water_quota',
            'update_water_quota',
            'delete_water_quota',
            'view_water_quota',
            'view_any_water_quota',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'sanctum'
            ]);
        }
    }
}