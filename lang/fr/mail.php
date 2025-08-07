<?php

return [

    /**
     * This Translation For LowWaterLevelMail.php
     */
    'low_water' => [
        'subject' => 'Alerte : Niveau d\'eau bas dans la source :sourceName',
        'greeting' => 'Bonjour,',
        'intro' => 'Nous vous informons que le niveau d\'eau dans la source **:sourceName** a atteint un seuil critique.',
        'capacity' => 'Capacité journalière :',
        'extracted' => 'Total extrait aujourd\'hui :',
        'action_needed' => 'Veuillez prendre les mesures nécessaires.',
        'regards' => 'Merci,',
    ],

     'quality_report' => [
        // Lignes d'objet
        'subject_success' => 'Rapport de qualité de l\'eau réussi pour la source : :sourceName',
        'subject_failure' => 'Alerte : Échec du test de qualité de l\'eau pour la source : :sourceName',

        // Général
        'title' => 'Rapport sur la qualité de l\'eau',
        'greeting' => 'Bonjour,',
        'intro' => 'Un nouveau test de qualité de l\'eau a été enregistré/mis à jour pour la source **:sourceName**. Voici les détails complets.',
        'thank_you' => 'Merci,',
        'regards_team' => 'L\'équipe :appName',

        // Sections
        'source_info_header' => 'Informations sur la source',
        'results_header' => 'Résultats du test',
        'failed_params_header' => 'Détails des paramètres non conformes',
        'failed_params_intro' => 'Les valeurs suivantes ont été enregistrées en dehors de la plage autorisée :',

        // Étiquettes
        'source_name' => 'Nom de la source :',
        'source_type' => 'Type de source :',
        'source_status' => 'État de la source :',
        'test_date' => 'Date du test :',
        'final_status' => 'État final du test :',

        // Statuts
        'status_passed' => 'Réussi',
        'status_failed' => 'Échoué',
        'not_recorded' => 'Non enregistré',
        'not_applicable' => 'S/O', // Sans Objet

        // Paramètres
        'ph_level' => 'Niveau de pH',
        'dissolved_oxygen' => 'Oxygène dissous',
        'total_dissolved_solids' => 'Solides dissous totaux (TDS)',
        'turbidity' => 'Turbidité',
        'temperature' => 'Température',
        'chlorine' => 'Chlore',
        'nitrate' => 'Nitrate',
        'total_coliform_bacteria' => 'Bactéries coliformes totales',

        // En-têtes de tableau pour les paramètres échoués
        'table_header_parameter' => 'Paramètre',
        'table_header_value' => 'Valeur enregistrée',
        'table_header_min' => 'Minimum autorisé',
        'table_header_max' => 'Maximum autorisé',
    ],
    'test_failed_notification' => [
        'subject' => 'Alerte immédiate : Écart dans le test de qualité de l\'eau pour :sourceName',
        'greeting' => 'Bonjour :name,',
        'intro' => 'Un test de qualité de l\'eau pour la source **:sourceName** a été enregistré avec des résultats non conformes aux normes requises.',
        'details_intro' => 'Détails des écarts enregistrés :',
        'test_id' => 'ID du test :',
        'water_source' => 'Source d\'eau :',
        'source_type' => 'Type de source :',
        'test_date' => 'Date du test :',
        'parameter' => 'Paramètre :',
        'recorded_value' => 'Valeur enregistrée :',
        'allowed_range' => 'Plage autorisée :',
        'action_needed' => 'Veuillez examiner les détails ci-dessus et prendre les mesures nécessaires.',
        'ph_level' => 'Niveau de pH',
        'dissolved_oxygen' => 'Oxygène dissous',
        'total_dissolved_solids' => 'Solides dissous totaux (TDS)',
        'turbidity' => 'Turbidité',
        'temperature' => 'Température',
        'chlorine' => 'Chlore',
        'nitrate' => 'Nitrate',
        'total_coliform_bacteria' => 'Bactéries coliformes totales',
    ],
];
