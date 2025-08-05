<?php

namespace Modules\WaterSources\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\WaterSources\Models\WaterQualityTest;

class WaterTestFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public WaterQualityTest $test;
    public array $failedParameters;

    /**
     * Create a new notification instance.
     *
     * @param WaterQualityTest $test The test that failed.
     * @param array $failedParameters Details of the parameters that failed.
     */
    public function __construct(WaterQualityTest $test, array $failedParameters)
    {
        $this->test = $test;
        $this->failedParameters = $failedParameters;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param object $notifiable
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param object $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        
        $mail = (new MailMessage)
                    ->error()
                    ->subject('Immediate Alert: Water Quality Test Deviation')
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line('A water quality test has been recorded with results that do not meet the required standards. All details are provided below.')
                    ->line('---')
                    ->line('**Test ID:** ' . $this->test->id) // إضافة ID الاختبار للمرجعية
                    ->line('**Water Source:** ' . $this->test->waterSource->name)
                    ->line('**Source Type:** ' . $this->test->waterSource->source)
                    ->line('**Test Date:** ' . $this->test->test_date->format('Y-m-d H:i'))
                    ->line('---')
                    ->line('**Details of the recorded deviations:**');

        foreach ($this->failedParameters as $param) {
            $mail->line(
                "- **Parameter:** {$param['parameter']} | ".
                "**Recorded Value:** {$param['value_recorded']} | ".
                "**Allowed Range:** [{$param['minimum_allowed']} - {$param['maximum_allowed']}]"
            );
        }

        $mail->line('---')
             ->line('Please review the details above and take the necessary action.');

        return $mail;
    }



    /**
     * Get the array representation of the notification.
     * (Optional, but good practice for database or broadcast channels).
     *
     * @param object $notifiable
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'test_id' => $this->test->id,
            'water_source_name' => $this->test->waterSource->name,
            'message' => "A water quality test for {$this->test->waterSource->name} has failed. Details sent via email.",
            'failed_parameters' => $this->failedParameters,
            'url' => null,
        ];
    }
}
