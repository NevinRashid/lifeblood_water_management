<?php

namespace Modules\WaterDistributionOperations\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\DistributionNetwork\Models\Reservoir;
use Illuminate\Notifications\Messages\MailMessage;

class ReservoirCriticalLevelNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public Reservoir $reservoir;
    public float $currentLevel;

    public function __construct(Reservoir $reservoir, float $currentLevel)
    {
        $this->reservoir = $reservoir;
        $this->currentLevel = $currentLevel;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail' , 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("Alert:Water level has dropped in the reservoir {$this->reservoir->name}")
            ->line("Current Level: {$this->currentLevel} m³")
            ->line("Critical Threshold: {$this->reservoir->minimum_critical_level} m³")
            ->line("Immediate action is required to prevent service disruption.");
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'reservoir_id' => $this->reservoir->id,
            'name' => $this->reservoir->name,
            'current_level' => $this->currentLevel,
            'critical_level' => $this->reservoir->minimum_critical_level,
            'message' => "Water level in reservoir #{$this->reservoir->id} has dropped to {$this->currentLevel}, which is below the critical threshold ({$this->reservoir->minimum_critical_level})."        ];
    }
}
