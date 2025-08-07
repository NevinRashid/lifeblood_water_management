<?php

namespace Modules\WaterSources\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App; // استيراد App
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
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param object $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        $sourceName = $this->test->waterSource->getTranslation('name', App::getLocale());

        $mail = (new MailMessage)
            ->error()
            ->subject(__('mail.test_failed_notification.subject', ['sourceName' => $sourceName]))
            ->greeting(__('mail.test_failed_notification.greeting', ['name' => $notifiable->name]))
            ->line(__('mail.test_failed_notification.intro', ['sourceName' => $sourceName]))
            ->line('---')
            ->line('**' . __('mail.test_failed_notification.test_id') . '** ' . $this->test->id)
            ->line('**' . __('mail.test_failed_notification.water_source') . '** ' . $sourceName)
            ->line('**' . __('mail.test_failed_notification.source_type') . '** ' . $this->test->waterSource->source)
            ->line('**' . __('mail.test_failed_notification.test_date') . '** ' . $this->test->test_date->format('Y-m-d H:i'))
            ->line('---')
            ->line('**' . __('mail.test_failed_notification.details_intro') . '**');

        foreach ($this->failedParameters as $param) {
            $parameterName = __('mail.test_failed_notification.' . $param['parameter']);

            $mail->line(
                "- **" . __('mail.test_failed_notification.parameter') . "** {$parameterName} | " .
                "**" . __('mail.test_failed_notification.recorded_value') . "** {$param['value_recorded']} | " .
                "**" . __('mail.test_failed_notification.allowed_range') . "** [{$param['minimum_allowed']} - {$param['maximum_allowed']}]"
            );
        }

        $mail->line('---')
             ->line(__('mail.test_failed_notification.action_needed'));

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
