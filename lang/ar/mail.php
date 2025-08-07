<?php

return [

    /**
     * This Translation For LowWaterLevelMail.php
     */
    'low_water' => [
        'subject' => 'تنبيه: انخفاض مستوى المياه في المصدر :sourceName',
        'greeting' => 'تحية طيبة،',
        'intro' => 'نود إعلامكم بأن مستوى المياه في المصدر **:sourceName** قد وصل إلى مستوى حرج.',
        'capacity' => 'القدرة الاستيعابية اليومية:',
        'extracted' => 'إجمالي ما تم سحبه اليوم:',
        'action_needed' => 'يرجى اتخاذ الإجراءات اللازمة.',
        'regards' => 'شكراً،',
    ],
     'quality_report' => [
        // Subject Lines
        'subject_success' => 'تقرير جودة مياه ناجح للمصدر: :sourceName',
        'subject_failure' => 'تنبيه: فشل اختبار جودة المياه للمصدر: :sourceName',

        // General
        'title' => 'تقرير جودة المياه',
        'greeting' => 'مرحباً،',
        'intro' => 'تم تسجيل/تحديث اختبار جودة مياه جديد للمصدر **:sourceName**. وفيما يلي التفاصيل الكاملة.',
        'thank_you' => 'شكراً لك،',
        'regards_team' => 'فريق :appName',

        // Sections
        'source_info_header' => 'معلومات المصدر',
        'results_header' => 'نتائج الاختبار',
        'failed_params_header' => 'تفاصيل المعايير غير المطابقة',
        'failed_params_intro' => 'تم رصد القيم التالية خارج النطاق المسموح به:',

        // Labels
        'source_name' => 'اسم المصدر:',
        'source_type' => 'نوع المصدر:',
        'source_status' => 'حالة المصدر:',
        'test_date' => 'تاريخ الاختبار:',
        'final_status' => 'الحالة النهائية للاختبار:',

        // Statuses
        'status_passed' => 'ناجح',
        'status_failed' => 'فاشل',
        'not_recorded' => 'لم تسجل',
        'not_applicable' => 'لا يوجد',

        // Parameters
        'ph_level' => 'درجة الحموضة (pH)',
        'dissolved_oxygen' => 'الأكسجين المذاب',
        'total_dissolved_solids' => 'إجمالي المواد الصلبة الذائبة (TDS)',
        'turbidity' => 'العكارة',
        'temperature' => 'درجة الحرارة',
        'chlorine' => 'الكلور',
        'nitrate' => 'النترات',
        'total_coliform_bacteria' => 'بكتيريا القولونيات',

        // Table Headers for failed parameters
        'table_header_parameter' => 'المعيار',
        'table_header_value' => 'القيمة المسجلة',
        'table_header_min' => 'الحد الأدنى المسموح',
        'table_header_max' => 'الحد الأعلى المسموح',
    ],
     'test_failed_notification' => [
        'subject' => 'تنبيه فوري: انحراف في اختبار جودة المياه للمصدر :sourceName',
        'greeting' => 'مرحباً :name،',
        'intro' => 'تم تسجيل اختبار جودة مياه للمصدر **:sourceName** بنتائج لا تتوافق مع المعايير المطلوبة.',
        'details_intro' => 'تفاصيل الانحرافات المسجلة:',
        'test_id' => 'معرّف الاختبار:',
        'water_source' => 'مصدر المياه:',
        'source_type' => 'نوع المصدر:',
        'test_date' => 'تاريخ الاختبار:',
        'parameter' => 'المعيار:',
        'recorded_value' => 'القيمة المسجلة:',
        'allowed_range' => 'النطاق المسموح به:',
        'action_needed' => 'يرجى مراجعة التفاصيل أعلاه واتخاذ الإجراء اللازم.',
        'ph_level' => 'درجة الحموضة (pH)',
        'dissolved_oxygen' => 'الأكسجين المذاب',
        'total_dissolved_solids' => 'إجمالي المواد الصلبة الذائبة (TDS)',
        'turbidity' => 'العكارة',
        'temperature' => 'درجة الحرارة',
        'chlorine' => 'الكلور',
        'nitrate' => 'النترات',
        'total_coliform_bacteria' => 'بكتيريا القولونيات',
    ],
];
