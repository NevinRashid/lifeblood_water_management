@component('mail::message')
# Water Quality Report

Hello,

A new water quality test has been recorded/updated for the source **{{ $test->waterSource->name }}**. The full details are below.

---

### **Source Information**
- **Source Name:** {{ $test->waterSource->name }}
- **Source Type:** {{ $test->waterSource->source }}
- **Source Status:** {{ $test->waterSource->status }}
- **Test Date:** {{ $test->test_date->format('Y-m-d H:i') }}

---

### **Test Results**
@component('mail::panel')
**Final Test Status: {{ $test->meets_standard_parameters ? 'Passed' : 'Failed' }}**
@endcomponent

- **pH Level:** `{{ $test->ph_level ?? 'Not Recorded' }}`
- **Dissolved Oxygen:** `{{ $test->dissolved_oxygen ?? 'Not Recorded' }}`
- **Total Dissolved Solids (TDS):** `{{ $test->total_dissolved_solids ?? 'Not Recorded' }}`
- **Turbidity:** `{{ $test->turbidity ?? 'Not Recorded' }}`
- **Temperature:** `{{ $test->temperature ?? 'Not Recorded' }}`
- **Chlorine:** `{{ $test->chlorine ?? 'Not Recorded' }}`
- **Nitrate:** `{{ $test->nitrate ?? 'Not Recorded' }}`
- **Total Coliform Bacteria:** `{{ $test->total_coliform_bacteria ?? 'Not Recorded' }}`

---

{{-- This section only appears if the test failed and there are failure details --}}
@if(!$test->meets_standard_parameters && !empty($failedParameters))
## Details of Failed Parameters

The following values were recorded outside the allowed range:

@component('mail::table')
| Parameter | Recorded Value | Allowed Minimum | Allowed Maximum |
|:----------------|:------------------|:----------------------|:---------------------|
@foreach($failedParameters as $failure)
| **{{ $failure['parameter_name'] }}** | `{{ $failure['recorded_value'] }}` | `{{ $failure['allowed_minimum'] ?? 'N/A' }}` | `{{ $failure['allowed_maximum'] ?? 'N/A' }}` |
@endforeach
@endcomponent
@endif


Thank you,<br>
The {{ config('app.name') }} Team
@endcomponent
