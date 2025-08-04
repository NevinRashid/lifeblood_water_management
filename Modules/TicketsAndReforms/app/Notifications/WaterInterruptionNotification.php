<?php

namespace Modules\TicketsAndReforms\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\TicketsAndReforms\Models\TroubleTicket;

class WaterInterruptionNotification extends Notification
{
    use Queueable;
    public $troubleTicket;
    /**
     * Create a new notification instance.
     */
    public function __construct(TroubleTicket $troubleTicket)
    {
        $this->troubleTicket = $troubleTicket;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('ًWater Supply Interruption')
            ->line("Dear Citizen,")
            ->line("We Would like to inform you that the water supply in your area, served by the {$this->troubleTicket->network} distribution network")
            ->line('Lifeblood Water Management Team!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'network'   => $this->troubleTicket->network,
            'location'  => $this->troubleTicket->location,
            'subject'   => $this->troubleTicket->subject,
        ];
    }
}
