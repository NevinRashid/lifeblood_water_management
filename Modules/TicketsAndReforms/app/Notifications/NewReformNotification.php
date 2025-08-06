<?php

namespace Modules\TicketsAndReforms\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\TicketsAndReforms\Models\Reform;

class NewReformNotification extends Notification
{
    use Queueable;

    public $reform;
    /**
     * Create a new notification instance.
     */
    public function __construct(Reform $reform)
    {
        $this->reform = $reform;
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
            ->subject('New Reform')
            ->line("There is a new reform assigned for your team {$this->reform->team->name}")
            ->line("The trouble ticket in the distribution network {$this->reform->ticket->network}")
            ->line("The location of the trouble that needs to be reformed is ({$this->reform->ticket->location?->latitude} , {$this->reform->ticket->location?->longitude})")
            ->line("The trouble is a {$this->reform->ticket->subject}");
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'team'      => $this->reform->team->name,
            'network'   => $this->reform->ticket->network,
            'location'  => $this->reform->ticket?->location,
            'subject'   => $this->reform->ticket->subject,
        ];
    }
}
