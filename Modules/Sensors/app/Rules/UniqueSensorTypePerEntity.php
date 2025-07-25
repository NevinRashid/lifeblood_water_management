<?php

namespace Modules\Sensors\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Modules\Sensors\Models\Sensor;

class UniqueSensorTypePerEntity implements ValidationRule
{
    protected string $sensorableType;
    protected  int $sensorableId;
    protected ?int $currentSensorId = null; // Optional sensor ID to exclude during update

    /**
     * Constructor accepts the entity type, entity ID,
     * and optionally the current sensor ID to exclude in update validation.
     *
     * @param string $sensorableType The type of the related entity (e.g. 'valve', 'pipe', 'pumpingstation')
     * @param int $sensorableId The ID of the related entity
     * @param int|null $currentSensorId The sensor ID to exclude from uniqueness check (used in update)
     */
    public function __construct(string $sensorableType, int $sensorableId, ?int $currentSensorId = null)
    {
        $this->sensorableType = $sensorableType;
        $this->sensorableId = $sensorableId;
        $this->currentSensorId = $currentSensorId;
    }

    /**
     * Run the validation rule.
     *
     * @param string $attribute The attribute name under validation (e.g. 'sensor_type')
     * @param mixed $value The value of the attribute under validation (sensor type)
     * @param Closure $fail Callback to invoke on validation failure
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // If no value is given (e.g., in update and 'sensor_type' is not sent), skip validation
        if (empty($value)) {
            return;
        }

        // Normalize the sensorable type to lowercase as stored in DB
        $type = strtolower($this->sensorableType);

        // Query to check if a sensor of the given type already exists for the specified entity
        $query = Sensor::where('sensorable_type', $type)
            ->where('sensorable_id', $this->sensorableId)
            ->where('sensor_type', $value);

        // If updating, exclude the current sensor from the uniqueness check
        if ($this->currentSensorId !== null) {
            $query->where('id', '!=', $this->currentSensorId);
        }

        // Check existence
        $exists = $query->exists();

        if ($exists) {
            $fail("A sensor of type '{$value}' already exists for this {$type}.");
        }
    }
}
