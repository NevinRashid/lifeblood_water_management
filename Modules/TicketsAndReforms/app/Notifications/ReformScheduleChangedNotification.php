<?php

namespace Modules\TicketsAndReforms\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\TicketsAndReforms\Models\Reform;

class ReformScheduleChangedNotification extends Notification
{
    use Queueable;

    public $reform;
    public $changedFields;
    /**
     * Create a new notification instance.
     */
    public function __construct(Reform $reform , array $changedFields)
    {
        $this->reform = $reform;
        $this->changedFields = $changedFields;
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
        $msg = (new MailMessage)
            ->subject("Schedule Update for Reform {$this->reform->id}")
            ->line("The reform schedule for the trouble {$this->reform->trouble_ticket_id}
                    in the distripution network {$this->reform->ticket->network} has been updated.");

            if (in_array('expected_start_date', $this->changedFields)) {
            $msg->line("New expected start date: {$this->reform->expected_start_date}");
            }

            if (in_array('expected_end_date', $this->changedFields)) {
            $msg->line("New expected end date: {$this->reform->expected_end_date}");
            }

        return $msg;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        $data= [
            'reform_id'         => $this->reform->id,
            'network'           => $this->reform->ticket->network,
            'trouble_ticket_id' => $this->reform->trouble_ticket_id,
        ];

        if(in_array('expected_start_date', $this->changedFields)) {
            $data['expected_start_date'] = $this->reform->expected_start_date;
        }

        if(in_array('expected_end_date', $this->changedFields)) {
            $data['expected_end_date'] = $this->reform->expected_end_date;
        }

        return $data;
    }
}
