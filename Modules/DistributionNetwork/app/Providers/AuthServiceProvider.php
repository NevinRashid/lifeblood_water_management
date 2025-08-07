<?php

namespace Modules\DistributionNetwork\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\UsersAndTeams\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Defining multiple permissions with the same logic
        foreach (['update_distribution_network_component', 'delete_distribution_network_component'] as $ability) {
            Gate::define($ability, function (User $user, $model , $ability) {

                //Allow if the user is an administrator of the network associated with the model.
                return $user->can($ability) && optional($model->network)->manager_id === $user->id;
            });
        }

        Gate::define('create_distribution_network_component', function (User $user,  $network) {

            //Verify that the user is a network administrator.
            return $user->can('create_distribution_network_component') && $network->manager_id === $user->id;
        });
    }
}
