<?php

namespace Modules\WaterDistributionOperations\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\DistributionNetwork\Models\DistributionPoint;
use Modules\WaterDistributionOperations\Models\DeliveryRoute;
use Modules\WaterDistributionOperations\Models\RouteDelivered;

class CanceledDeliveryRouteNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected DeliveryRoute $deliveryRoute;
    protected DistributionPoint $distributionPoint;
    protected RouteDelivered $routeDelivery;

    /**
     * Create a new notification instance.
     *
     * @param DeliveryRoute $deliveryRoute
     * @param DistributionPoint $distributionPoint
     * @param RouteDelivered $routeDelivery
     * @return void
     */
    public function __construct(DeliveryRoute $deliveryRoute, DistributionPoint $distributionPoint, RouteDelivered $routeDelivery)
    {
       
        $this->deliveryRoute = $deliveryRoute;
        $this->distributionPoint = $distributionPoint;
        $this->routeDelivery = $routeDelivery;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via(mixed $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Alert: Delivery Route Canceled')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Please be informed that **Delivery Route ' . $this->deliveryRoute->id . '** for the distribution point "' . $this->distributionPoint->name . '" has been canceled. As a result, this area will be deprived of water.')
            ->line('---') // Horizontal line for better separation
            ->line('### **Full Details:**')
            ->line('**Distribution Point Details:**')
            ->line('- **Name:** ' . $this->distributionPoint->name)
            ->line('- **Location:** ' . $this->distributionPoint->location) // Note: Consider formatting this (e.g., using a library to convert to coordinates).
            ->line('- **Network:** ' . $this->distributionPoint->network->name)
            ->line('---')
            ->line('**Canceled Delivery Details:**')
            ->line('- **Water Amount (expected):** ' . $this->routeDelivery->water_amount_delivered)
            ->line('- **Expected Arrival Time:** ' . optional($this->routeDelivery->arrival_time)->format('Y-m-d H:i') ?? 'Not specified')
            ->line('- **Notes:** ' . ($this->routeDelivery->notes ?? 'No notes provided'))
            ->line('---')
            ->line('Please take the necessary actions to ensure water is supplied to the affected area.')
            ->salutation('Best regards,' . PHP_EOL . config('app.name'));
    }
    /**
     * Get the array representation of the notification.
     */
    public function toArray(mixed $notifiable): array
    {
        return [
            'subject' => 'Delivery Route Canceled',
            'route_id' => $this->deliveryRoute->id,
            'distribution_point' => [
                'name' => $this->distributionPoint->name,
                'location' => $this->distributionPoint->location,
                'network_name' => $this->distributionPoint->network->name,
            ],
            'canceled_delivery' => [
                'water_amount_delivered' => $this->routeDelivery->water_amount_delivered,
                'arrival_time' => optional($this->routeDelivery->arrival_time)->format('Y-m-d H:i'),
                'notes' => $this->routeDelivery->notes,
            ],
            'manager_name' => $notifiable->name,
            'message' => 'The delivery route for ' . $this->distributionPoint->name . ' has been canceled, potentially leading to water deprivation.',
        ];
    }
}
