<?php

return [

    /**
     * This Translation For LowWaterLevelMail.php
     */
    'low_water' => [
        'subject' => 'Внимание: Низкий уровень воды в источнике :sourceName',
        'greeting' => 'Здравствуйте,',
        'intro' => 'Сообщаем вам, что уровень воды в источнике **:sourceName** достиг критической отметки.',
        'capacity' => 'Суточная производительность:',
        'extracted' => 'Всего извлечено за сегодня:',
        'action_needed' => 'Пожалуйста, примите необходимые меры.',
        'regards' => 'С уважением,',
    ],
     'quality_report' => [
        // Темы письма
        'subject_success' => 'Успешный отчет о качестве воды для источника: :sourceName',
        'subject_failure' => 'Внимание: Сбой проверки качества воды для источника: :sourceName',

        // Общее
        'title' => 'Отчет о качестве воды',
        'greeting' => 'Здравствуйте,',
        'intro' => 'Для источника **:sourceName** была записана/обновлена новая проверка качества воды. Ниже приведены полные сведения.',
        'thank_you' => 'Спасибо,',
        'regards_team' => 'Команда :appName',

        // Разделы
        'source_info_header' => 'Информация об источнике',
        'results_header' => 'Результаты проверки',
        'failed_params_header' => 'Сведения о несоответствующих параметрах',
        'failed_params_intro' => 'Следующие значения были зафиксированы вне допустимого диапазона:',

        // Метки
        'source_name' => 'Название источника:',
        'source_type' => 'Тип источника:',
        'source_status' => 'Статус источника:',
        'test_date' => 'Дата проверки:',
        'final_status' => 'Итоговый статус проверки:',

        // Статусы
        'status_passed' => 'Пройдено',
        'status_failed' => 'Сбой',
        'not_recorded' => 'Не записано',
        'not_applicable' => 'Н/Д', // Нет данных

        // Параметры
        'ph_level' => 'Уровень pH',
        'dissolved_oxygen' => 'Растворенный кислород',
        'total_dissolved_solids' => 'Общее количество растворенных твердых веществ (TDS)',
        'turbidity' => 'Мутность',
        'temperature' => 'Температура',
        'chlorine' => 'Хлор',
        'nitrate' => 'Нитраты',
        'total_coliform_bacteria' => 'Общие колиформные бактерии',

        // Заголовки таблицы для неудачных параметров
        'table_header_parameter' => 'Параметр',
        'table_header_value' => 'Записанное значение',
        'table_header_min' => 'Допустимый минимум',
        'table_header_max' => 'Допустимый максимум',
    ],
      'test_failed_notification' => [
        'subject' => 'Срочное оповещение: Отклонение в тесте качества воды для :sourceName',
        'greeting' => 'Здравствуйте, :name,',
        'intro' => 'Для источника **:sourceName** был зафиксирован тест качества воды с результатами, не соответствующими требуемым стандартам.',
        'details_intro' => 'Детали зафиксированных отклонений:',
        'test_id' => 'ID теста:',
        'water_source' => 'Источник воды:',
        'source_type' => 'Тип источника:',
        'test_date' => 'Дата теста:',
        'parameter' => 'Параметр:',
        'recorded_value' => 'Записанное значение:',
        'allowed_range' => 'Допустимый диапазон:',
        'action_needed' => 'Пожалуйста, ознакомьтесь с деталями выше и примите необходимые меры.',
        'ph_level' => 'Уровень pH',
        'dissolved_oxygen' => 'Растворенный кислород',
        'total_dissolved_solids' => 'Общее количество растворенных твердых веществ (TDS)',
        'turbidity' => 'Мутность',
        'temperature' => 'Температура',
        'chlorine' => 'Хлор',
        'nitrate' => 'Нитраты',
        'total_coliform_bacteria' => 'Общие колиформные бактерии',
    ],
];
