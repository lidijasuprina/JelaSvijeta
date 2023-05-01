<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Controllers\MealController;
use App\Http\Requests\MealRequest;

class MealServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(MealController::class, function ($app) {
            return new MealController(new MealRequest);
        });
    }
}
