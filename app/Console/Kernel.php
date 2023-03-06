<?php

namespace App\Console;

use App\Models\Analyzer;
use App\Models\User;
use App\Models\Orders;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('users:total')->monthlyOn(1, '00:00');

        $schedule->call(function () {
            $date = Carbon::now();

            $users_numb = User::get();
            $orders_numb = Orders::get();


            Analyzer::create([
                'data_name' => 'users_total',
                'number' => $users_numb->count(),
                'date' => $date->format('F')
            ]);

            Analyzer::create([
                'data_name' => 'orders_total',
                'number' => $orders_numb->count(),
                'date' => $date->format('F')
            ]);
        })->monthlyOn(1, '00:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
