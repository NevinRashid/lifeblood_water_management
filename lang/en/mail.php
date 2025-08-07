<?php

return [

    /**
     * This Translation For LowWaterLevelMail.php
     */
    'low_water' => [
        'subject' => 'Alert: Low water level in source :sourceName',
        'greeting' => 'Hello,',
        'intro' => 'We would like to inform you that the water level in the source **:sourceName** has reached a critical threshold.',
        'capacity' => 'Daily Capacity:',
        'extracted' => 'Total Extracted Today:',
        'action_needed' => 'Please take the necessary actions.',
        'regards' => 'Regards,',
    ],
    'quality_report' => [
        // Subject Lines
        'subject_success' => 'Successful Water Quality Report for Source: :sourceName',
        'subject_failure' => 'Alert: Water Quality Test Failed for Source: :sourceName',

        // General
        'title' => 'Water Quality Report',
        'greeting' => 'Hello,',
        'intro' => 'A new water quality test has been recorded/updated for the source **:sourceName**. The full details are below.',
        'thank_you' => 'Thank you,',
        'regards_team' => 'The :appName Team',

        // Sections
        'source_info_header' => 'Source Information',
        'results_header' => 'Test Results',
        'failed_params_header' => 'Details of Non-Compliant Parameters',
        'failed_params_intro' => 'The following values were recorded outside the allowed range:',

        // Labels
        'source_name' => 'Source Name:',
        'source_type' => 'Source Type:',
        'source_status' => 'Source Status:',
        'test_date' => 'Test Date:',
        'final_status' => 'Final Test Status:',

        // Statuses
        'status_passed' => 'Passed',
        'status_failed' => 'Failed',
        'not_recorded' => 'Not Recorded',
        'not_applicable' => 'N/A',

        // Parameters
        'ph_level' => 'pH Level',
        'dissolved_oxygen' => 'Dissolved Oxygen',
        'total_dissolved_solids' => 'Total Dissolved Solids (TDS)',
        'turbidity' => 'Turbidity',
        'temperature' => 'Temperature',
        'chlorine' => 'Chlorine',
        'nitrate' => 'Nitrate',
        'total_coliform_bacteria' => 'Total Coliform Bacteria',

        // Table Headers for failed parameters
        'table_header_parameter' => 'Parameter',
        'table_header_value' => 'Recorded Value',
        'table_header_min' => 'Allowed Minimum',
        'table_header_max' => 'Allowed Maximum',
    ],

     'test_failed_notification' => [
        'subject' => 'Immediate Alert: Water Quality Test Deviation for :sourceName',
        'greeting' => 'Hello :name,',
        'intro' => 'A water quality test for source **:sourceName** has been recorded with results that do not meet the required standards.',
        'details_intro' => 'Details of the recorded deviations:',
        'test_id' => 'Test ID:',
        'water_source' => 'Water Source:',
        'source_type' => 'Source Type:',
        'test_date' => 'Test Date:',
        'parameter' => 'Parameter:',
        'recorded_value' => 'Recorded Value:',
        'allowed_range' => 'Allowed Range:',
        'action_needed' => 'Please review the details above and take the necessary action.',
        'ph_level' => 'pH Level',
        'dissolved_oxygen' => 'Dissolved Oxygen',
        'total_dissolved_solids' => 'Total Dissolved Solids (TDS)',
        'turbidity' => 'Turbidity',
        'temperature' => 'Temperature',
        'chlorine' => 'Chlorine',
        'nitrate' => 'Nitrate',
        'total_coliform_bacteria' => 'Total Coliform Bacteria',
    ],

];
