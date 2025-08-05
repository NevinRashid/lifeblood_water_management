@component('mail::message')
# تقرير جودة المياه

مرحباً,

تم تسجيل/تحديث اختبار جودة مياه جديد لمصدر **{{ $test->waterSource->name }}**، وفيما يلي التفاصيل الكاملة.

---

### **معلومات المصدر**
- **اسم المصدر:** {{ $test->waterSource->name }}
- **نوع المصدر:** {{ $test->waterSource->source }}
- **حالة المصدر:** {{ $test->waterSource->status }}
- **تاريخ الاختبار:** {{ $test->test_date->format('Y-m-d H:i') }}

---

### **نتائج الاختبار**
@component('mail::panel')
**الحالة النهائية للاختبار: {{ $test->meets_standard_parameters ? 'ناجح' : 'فاشل' }}**
@endcomponent

- **درجة الحموضة (pH):** `{{ $test->ph_level ?? 'لم تسجل' }}`
- **الأكسجين المذاب (dissolved_oxygen):** `{{ $test->dissolved_oxygen ?? 'لم تسجل' }}`
- **إجمالي المواد الصلبة الذائبة (TDS):** `{{ $test->total_dissolved_solids ?? 'لم تسجل' }}`
- **العكارة (turbidity):** `{{ $test->turbidity ?? 'لم تسجل' }}`
- **درجة الحرارة (temperature):** `{{ $test->temperature ?? 'لم تسجل' }}`
- **الكلور (chlorine):** `{{ $test->chlorine ?? 'لم تسجل' }}`
- **النترات (nitrate):** `{{ $test->nitrate ?? 'لم تسجل' }}`
- **بكتيريا القولونيات (total_coliform_bacteria):** `{{ $test->total_coliform_bacteria ?? 'لم تسجل' }}`

---

{{-- هذا الجزء يظهر فقط إذا فشل الاختبار وكانت هناك تفاصيل للفشل --}}
@if(!$test->meets_standard_parameters && !empty($failedParameters))
## تفاصيل المعايير غير المطابقة

تم رصد القيم التالية خارج النطاق المسموح به:

@component('mail::table')
| المعيار | القيمة المسجلة | الحد الأدنى المسموح | الحد الأعلى المسموح |
|:----------------|:------------------|:----------------------|:---------------------|
@foreach($failedParameters as $failure)
| **{{ $failure['parameter'] }}** | `{{ $failure['value_recorded'] }}` | `{{ $failure['minimum_allowed'] ?? 'لا يوجد' }}` | `{{ $failure['maximum_allowed'] ?? 'لا يوجد' }}` |
@endforeach
@endcomponent
@endif


شكراً لك،<br>
فريق {{ config('app.name') }}
@endcomponent
