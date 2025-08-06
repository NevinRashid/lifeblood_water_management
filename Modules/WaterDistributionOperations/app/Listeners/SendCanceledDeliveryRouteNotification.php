<?php

namespace Modules\WaterDistributionOperations\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\WaterDistributionOperations\Events\DeliveryRouteCanceled;
use Modules\WaterDistributionOperations\Notifications\CanceledDeliveryRouteNotification;

class SendCanceledDeliveryRouteNotification
{

    /**
     * Create the event listener.
     * Handle the event.
     *
     * @param DeliveryRouteCanceled $event
     * @return void
     */
    public function handle(DeliveryRouteCanceled $event): void
    {
        $deliveryRoute = $event->deliveryRoute;


        // Fetch the related delivered  for the route.
        $delivered = $deliveryRoute->deliveries;
         
        // Loop through each distribution point to find its network manager.
        foreach ($delivered as $delivery) {
           
            // Check if the distribution point and its network exist.
            if ($delivery->distributionPoint->network && $delivery->distributionPoint->network->manager) {
               
                $manager = $delivery->distributionPoint->network->manager;
                
                // Send the notification to the network manager.
                $manager->notify(new CanceledDeliveryRouteNotification(
                    $deliveryRoute,
                    $delivery->distributionPoint,
                    $delivery,
                ));
            }
        }
    }
}
