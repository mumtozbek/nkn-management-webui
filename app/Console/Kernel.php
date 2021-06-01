<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\SyncUptime::class,
        Commands\SyncLocation::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Sync node information.
        $schedule->command('sync:uptime')->everyTenMinutes()->withoutOverlapping(3600);
        $schedule->command('sync:location')->everyMinute()->withoutOverlapping(3600);

        // Check wallets id every 30 minutes.
        $schedule->command('sync:wallet-id')->everyThirtyMinutes()->withoutOverlapping(3600);

        // Run scheduled jobs.
        $schedule->command('queue:work --stop-when-empty')->everyMinute()->withoutOverlapping(3600);
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
