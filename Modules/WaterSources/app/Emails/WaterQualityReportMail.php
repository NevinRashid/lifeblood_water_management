<?php

namespace Modules\WaterSources\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\App;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
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



     public function envelope(): Envelope
    {
        $locale = App::getLocale();
        $sourceName = $this->test->waterSource->getTranslation('name', $locale);

        $subjectKey = $this->test->meets_standard_parameters
            ? 'mail.quality_report.subject_success'
            : 'mail.quality_report.subject_failure';

        return new Envelope(
            subject: __($subjectKey, ['sourceName' => $sourceName]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $failedParameters = [];
        if (!$this->test->meets_standard_parameters) {
            $failedParameters = $this->getFailedParameters();
        }

        return new Content(
            markdown: 'emails.reports.water-quality',
            with: [
                'test' => $this->test,
                'failedParameters' => $failedParameters,
            ],
        );
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
