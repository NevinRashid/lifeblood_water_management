<?php

namespace Modules\TicketsAndReforms\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\TicketsAndReforms\Models\TroubleTicket;

class TroubleAcceptedNotification extends Notification
{
    use Queueable;

    public $trouble;
    /**
     * Create a new notification instance.
     */
    public function __construct(TroubleTicket $trouble)
    {
        $this->trouble = $trouble;
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
            ->subject('Your Report About Trouble Has Been Accepted')
            ->line("Dear {$this->trouble->reporter->name}")
            ->line('We have reviewed and confirmed the issue you reported, and is currently awaiting assignment for repair')
            ->line('Thank you for your valuable contribution in helping us improve the water sevices')
            ->line('Lifeblood Water Management!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'trouble_ticket_id' => $this->trouble->id,
            'reporter'          => $this->trouble->reporter,
        ];
    }
}
