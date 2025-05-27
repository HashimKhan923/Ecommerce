<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{


    protected $commands = [
        Commands\UpdatePayoutStatus::class,
        Commands\RemoveOldNotification::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('deals:update-status')->everyMinute();
        // $schedule->command('coupon:update')->hourly();
        $schedule->command('payout:update')->daily();
        $schedule->command('notify:back-in-stock')->everySixHours();
        $schedule->command('send:review-request')->daily();
        $schedule->command('orders:track-fedex')->everySixHours();
        $schedule->command('app:remove-old-notification')->monthly();
    }


    

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

}
