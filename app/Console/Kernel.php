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
        Commands\SyncMonitor::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Sync node information.
        $schedule->command('sync:uptime')->everyTenMinutes()->withoutOverlapping();
        $schedule->command('sync:monitor')->everyThirtyMinutes()->withoutOverlapping();
        $schedule->command('sync:location')->everyThirtyMinutes()->withoutOverlapping();

        // Run scheduled jobs.
        $schedule->command('queue:work --stop-when-empty')->everyMinute()->withoutOverlapping();

        // Need to run restart and reboot commands after all jobs.
        $schedule->command('restart:slow')->everyThirtyMinutes()->withoutOverlapping();
        $schedule->command('restart:reboot')->everyThirtyMinutes()->withoutOverlapping();
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
