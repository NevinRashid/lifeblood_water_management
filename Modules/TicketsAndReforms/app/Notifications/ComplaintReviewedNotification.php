<?php

namespace Modules\TicketsAndReforms\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\TicketsAndReforms\Models\TroubleTicket;

class ComplaintReviewedNotification extends Notification
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
            ->subject('Your Complaint Has Been Reviewed')
            ->greeting("Dear {$this->trouble->reporter->name}")
            ->line("Thank you for your feedback.")
            ->line("Your complaint has been reviewed by our team and taken into consideratuon.")
            ->line('We appreciate your effort in helping us improve the quality of water services');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'trouble_ticket_id' => $this->trouble->id,
            'reporter'          => $this->trouble->reporter,
            'status'            => $this->trouble->status,
        ];
    }
}
