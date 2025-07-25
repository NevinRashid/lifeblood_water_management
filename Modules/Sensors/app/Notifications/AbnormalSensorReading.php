<?php

namespace Modules\Sensors\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Sensors\Models\SensorReading;

class AbnormalSensorReading extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public SensorReading $reading,
        public string $abnormalityType
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Abnormal Sensor Reading Detected')
            ->line("Sensor {$this->reading->sensor->name} recorded abnormal value:")
            ->line("Value: {$this->reading->value} ({$this->reading->unit})")
            ->line("Status: " . ucfirst(str_replace('_', ' ', $this->abnormalityType)));
        //->action('View Dashboard', url('/dashboard'));
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'reading_id' => $this->reading->id,
            'abnormality_type' => $this->abnormalityType,
            'value' => $this->reading->value,
            'sensor_name' => $this->reading->sensor->name
        ];
    }
}
