<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Http\Controllers\TestScheduleController;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule) {
        // Jadwalkan perintah untuk mengirim notifikasi segera
        $schedule->command('notifications:send-immediate')->everyMinute();

        // Jadwalkan tugas lainnya
        $schedule->call(function () {
            (new TestScheduleController)->checkForNotifications();
        })->everyMinute();  
    }

    /**
     * Register the commands for the application.
     */
    protected function commands() {
        $this->load(__DIR__.'/Commands');

        // Register any additional commands here if needed
        // $this->command('your:command', 'YourCommandClass');a
    }
}
