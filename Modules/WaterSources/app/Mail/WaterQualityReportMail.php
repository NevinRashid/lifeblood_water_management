<?php

namespace Modules\WaterSources\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\App;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\WaterSources\Models\WaterQualityTest;

class WaterQualityReportMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The WaterQualityTest instance.
     *
     * @var \Modules\WaterSources\Models\WaterQualityTest
     */
    public WaterQualityTest $test;


    /**
     * Create a new message instance.
     *
     * @param \Modules\WaterSources\Models\WaterQualityTest $test
     */
    public function __construct(WaterQualityTest $test)
    {
        $this->test = $test;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = $this->test->meets_standard_parameters
            ? 'Successful Water Quality Report for Source: ' . $this->test->waterSource->name
            : 'Urgent Alert: Water Quality Test Failed for Source: ' . $this->test->waterSource->name;

        $failedParameters = [];
        if (!$this->test->meets_standard_parameters) {
            $failedParameters = $this->getFailedParameters();
        }
        return $this->subject($subject)
                    ->markdown('emails.reports.water-quality')
                    ->with([
                        'test' => $this->test,
                        'failedParameters' => $failedParameters,
                    ]);
    }


    private function getFailedParameters(): array
    {
        $failedDetails = [];
        $standards = $this->test->waterSource->parameters;

        foreach ($standards as $standard) {
            $columnName = $standard->name;
            $testValue = $this->test->$columnName;

            if ($testValue === null) continue;

            $isFailed = ($standard->minimum_level !== null && $testValue < $standard->minimum_level) ||
                        ($standard->maximum_level !== null && $testValue > $standard->maximum_level);

            if ($isFailed) {
                 $failedDetails[] = [
                    'parameter' => $columnName,
                    'value_recorded' => $testValue,
                    'minimum_allowed' => $standard->minimum_level,
                    'maximum_allowed' => $standard->maximum_level,
                ];
            }
        }
        return $failedDetails;
    }
}
