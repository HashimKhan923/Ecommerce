<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        'App\Events\SpecificErrorOccurred' => [
            'App\Listeners\LogEventListener',
        ],

        \App\Events\OrderPlaced::class => [
            \App\Listeners\CreateOrderTimeline::class,
            \App\Listeners\SendVendorEmail::class,
            \App\Listeners\NotifyVendor::class,
            \App\Listeners\StoreCouponData::class,
            \App\Listeners\UpdateCustomerSale::class,
            \App\Listeners\StoreOrderDetails::class,
            \App\Listeners\UpdateStockLevels::class,
            \App\Listeners\TrackFeaturedProducts::class,
            \App\Listeners\SendBuyerConfirmationEmail::class,

        ],
    
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
