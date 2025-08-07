<?php

return [

    /**
     * This Translation For LowWaterLevelMail.php
     */
    'low_water' => [
        'subject' => '警报：水源 :sourceName 水位过低',
        'greeting' => '您好，',
        'intro' => '我们特此通知，水源 **:sourceName** 的水位已达到临界阈值。',
        'capacity' => '每日容量：',
        'extracted' => '今日总取水量：',
        'action_needed' => '请采取必要措施。',
        'regards' => '此致，',
    ],
      'quality_report' => [
        // 主题行
        'subject_success' => '水源“:sourceName”水质报告 - 成功',
        'subject_failure' => '警报：水源“:sourceName”水质检测失败',

        // 通用
        'title' => '水质报告',
        'greeting' => '您好，',
        'intro' => '水源 **:sourceName** 已记录/更新一份新的水质检测报告。详细信息如下。',
        'thank_you' => '谢谢您，',
        'regards_team' => ':appName 团队',

        // 章节
        'source_info_header' => '水源信息',
        'results_header' => '检测结果',
        'failed_params_header' => '不合规参数详情',
        'failed_params_intro' => '检测到以下数值超出允许范围：',

        // 标签
        'source_name' => '水源名称：',
        'source_type' => '水源类型：',
        'source_status' => '水源状态：',
        'test_date' => '检测日期：',
        'final_status' => '最终检测状态：',

        // 状态
        'status_passed' => '通过',
        'status_failed' => '失败',
        'not_recorded' => '未记录',
        'not_applicable' => '不适用',

        // 参数
        'ph_level' => 'pH值',
        'dissolved_oxygen' => '溶解氧',
        'total_dissolved_solids' => '总溶解固体 (TDS)',
        'turbidity' => '浊度',
        'temperature' => '温度',
        'chlorine' => '氯',
        'nitrate' => '硝酸盐',
        'total_coliform_bacteria' => '总大肠菌群',

        // 不合格参数的表头
        'table_header_parameter' => '参数',
        'table_header_value' => '记录值',
        'table_header_min' => '允许最小值',
        'table_header_max' => '允许最大值',
    ],
     'test_failed_notification' => [
        'subject' => '紧急警报：水源“:sourceName”水质检测出现偏差',
        'greeting' => '您好，:name，',
        'intro' => '水源 **:sourceName** 的一项水质检测结果未达到规定标准。',
        'details_intro' => '记录的偏差详情如下：',
        'test_id' => '检测ID：',
        'water_source' => '水源：',
        'source_type' => '水源类型：',
        'test_date' => '检测日期：',
        'parameter' => '参数：',
        'recorded_value' => '记录值：',
        'allowed_range' => '允许范围：',
        'action_needed' => '请查看以上详细信息并采取必要措施。',
        'ph_level' => 'pH值',
        'dissolved_oxygen' => '溶解氧',
        'total_dissolved_solids' => '总溶解固体 (TDS)',
        'turbidity' => '浊度',
        'temperature' => '温度',
        'chlorine' => '氯',
        'nitrate' => '硝酸盐',
        'total_coliform_bacteria' => '总大肠菌群',
    ],
];
