<?php

namespace Modules\TicketsAndReforms\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewTroubleTicketNotification extends Notification
{
    use Queueable;
    public $troubleTicket;
    /**
     * Create a new notification instance.
     */
    public function __construct($troubleTicket)
    {
        $this->troubleTicket= $troubleTicket;
    }

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
            ->subject('New TroubleTicket')
            ->line("There is a new trouble ticket in the distribution network {$this->troubleTicket->network}")
            ->line("The location of the trouble ({$this->troubleTicket->location->latitude} , {$this->troubleTicket->location->longitude})")
            ->line("The trouble is a {$this->troubleTicket->subject}")
            ->line("The reporter for this trouble is {$this->troubleTicket->reporter->name}")
            ->line('Lifeblood Water Management!');
            //->action('Notification Action', 'https://laravel.com');
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
            'reporter'  => $this->troubleTicket->reporter->name
        ];
    }
}
