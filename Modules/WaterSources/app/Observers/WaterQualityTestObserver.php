<?php

namespace Modules\WaterSources\Observers;

use Modules\UsersAndTeams\Models\User;
use Illuminate\Support\Facades\Notification;
use Modules\WaterSources\Models\WaterQualityTest;
use Modules\WaterSources\Notifications\WaterTestFailedNotification;



class WaterQualityTestObserver
{
  
    public function created(WaterQualityTest $waterQualityTest): void
    {
        $this->sendNotificationIfFailed($waterQualityTest);
    }


    public function updated(WaterQualityTest $waterQualityTest): void
    {
        if ($waterQualityTest->isDirty('meets_standard_parameters')) {
            $this->sendNotificationIfFailed($waterQualityTest);
        }
    }


    private function sendNotificationIfFailed(WaterQualityTest $test): void
    {
        if ($test->meets_standard_parameters === false) {
            $waterSource = $test->waterSource()->with('parameters')->first();
            $failedParameters = $this->evaluateTest($test, $waterSource->parameters);

            if (empty($failedParameters)) {
                return;
            }

            $qualityEngineers = User::where('role', 'engineer')->get();

            if ($qualityEngineers->isNotEmpty()) {
                Notification::send($qualityEngineers, new WaterTestFailedNotification(
                    $test,
                    $failedParameters
                ));
            }
        }
    }

    private function evaluateTest(WaterQualityTest $test, $standards): array
    {
        $failedParametersDetails = [];

        foreach ($standards as $standard) {
            $columnName = $standard->name;
            if (!isset($test->$columnName)) continue;

            $testValue = $test->$columnName;
            $isFailed = false;

            if ($standard->minimum_level !== null && $testValue < $standard->minimum_level) $isFailed = true;
            if (!$isFailed && $standard->maximum_level !== null && $testValue > $standard->maximum_level) $isFailed = true;

            if ($isFailed) {
                $failedParametersDetails[] = [
                    'parameter' => $columnName,
                    'value_recorded' => $testValue,
                    'minimum_allowed' => $standard->minimum_level,
                    'maximum_allowed' => $standard->maximum_level,
                ];
            }
        }
        return $failedParametersDetails;
    }



    /**
     * Handle the WaterQualityTest "deleted" event.
     */
    public function deleted(WaterQualityTest $waterQualityTest): void
    {
        //
    }

    /**
     * Handle the WaterQualityTest "restored" event.
     */
    public function restored(WaterQualityTest $waterQualityTest): void
    {
        //
    }

    /**
     * Handle the WaterQualityTest "force deleted" event.
     */
    public function forceDeleted(WaterQualityTest $waterQualityTest): void
    {
        //
    }
}
