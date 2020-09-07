<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
//        'App\Events\SomeEvent' => [
//            'App\Listeners\EventListener',
//        ],
        'App\Events\OrderPayedEvent' => [
            'App\Listeners\OrderPayedEventListen'
        ],
        'App\Events\OrderRefundEvent' => [
            'App\Listeners\OrderRefundEventListen'
        ],
        'App\Events\NewUserEvent' => [
            'App\Listeners\NewUserEventListen'
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
