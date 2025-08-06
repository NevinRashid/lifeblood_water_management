<?php

namespace Modules\TicketsAndReforms\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\TicketsAndReforms\Models\TroubleTicket;

class TroubleRejectedNotification extends Notification
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
            ->subject('Your Trouble Report Has Been Rejected')
            ->greeting("Dear {$this->trouble->reporter->name}")
            ->line('Thank you for reporting the issue.')
            ->line('Our maintenance team has inspected the reported location but did not find any malfunction or problem that requires repair at this time.')
            ->line('We appreciate your vigilance and encourage you to continue reporting any concerns that may arise in the future.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [];
    }
}
