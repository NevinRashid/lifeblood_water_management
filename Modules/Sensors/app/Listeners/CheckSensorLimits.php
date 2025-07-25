<?php

namespace Modules\Sensors\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Sensors\Events\SensorReadingCreated;
use Modules\Sensors\Models\Sensor;

class CheckSensorLimits
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(SensorReadingCreated $event): void
    {
        $reading = $event->reading;
        $sensor = Sensor::with('sensorable')->find($reading->sensor_id);

        if (!$sensor || !$sensor->sensorable) {
            return;
        }

        $limits = $this->getLimitsForSensorType($sensor);
        $this->validateReading($reading, $limits, $event);
    }

    protected function getLimitsForSensorType(Sensor $sensor): array
    {
        return match ($sensor->sensor_type) {
            'pressure_sensor' => [
                'min' => $sensor->sensorable->min_pressure,
                'max' => $sensor->sensorable->max_pressure,
                'metric' => 'pressure'
            ],
            'flow_sensor' => [
                'min' => $sensor->sensorable->min_flow,
                'max' => $sensor->sensorable->max_flow,
                'metric' => 'flow'
            ],
            default => ['min' => null, 'max' => null, 'metric' => '']
        };
    }

    protected function validateReading($reading, array $limits, SensorReadingCreated $event)
    {
        if ($limits['min'] !== null && $reading->value < $limits['min']) {
            $event->isAbnormal = true;
            $event->abnormalityType = 'below_min';
        }

        if ($limits['max'] !== null && $reading->value > $limits['max']) {
            $event->isAbnormal = true;
            $event->abnormalityType = 'above_max';
        }
    }
}
