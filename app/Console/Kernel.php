<?php

namespace App\Console;

use Illuminate\Support\Facades\Cookie;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Services\SyncService;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        'App\Console\Commands\DatabaseBackUp',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
       
        $schedule->command('database:backup');
        
        // Sync every 15 minutes (adjust as needed)
        $schedule->call(function () {
            $syncService = app(SyncService::class);
            $result = $syncService->sync();
            
            // Log results
            \Log::info('Scheduled sync completed', [
                'pull_success' => $result['pull']['success'],
                'push_success' => $result['push']['success']
            ]);
        })->everyFifteenMinutes();

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
