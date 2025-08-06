<?php

namespace Modules\WaterSources\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class WaterExtractionNotification extends Notification
{
    use Queueable;

    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Water Extraction Event for ' . $this->mailData['networkName']) 
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('A new water extraction event has been completed for **' . $this->mailData['networkName'] . '**.') 
            ->line('The water source was **' . $this->mailData['source'] . '**. Below are the operation details:')
            ->line('Total Extracted: **' . number_format($this->mailData['extractedAmount'], 2) . ' m³**')
            ->line('Delivered to Network: **' . number_format($this->mailData['deliveredAmount'], 2) . ' m³**')
            ->line('Lost Amount: **' . number_format($this->mailData['lostAmount'], 2) . ' m³**')
            ->action('View Details on Dashboard', url('/dashboard'));
    }
    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'subject' => 'New Water Extraction Event for ' . $this->mailData['networkName'],
            'network_name' => $this->mailData['networkName'],
            'source' => $this->mailData['source'],
            'extracted_amount' => $this->mailData['extractedAmount'],
            'delivered_amount' => $this->mailData['deliveredAmount'],
            'lost_amount' => $this->mailData['lostAmount'],
            'extraction_date' => $this->mailData['extractionDate']->toDateTimeString(),
        ];
    }
}
