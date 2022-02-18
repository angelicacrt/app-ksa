<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\DailyReportItemBelowStockJob;
use App\Jobs\CalculateDailyOperationalBoatData;
use App\Jobs\SendEmailJob;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Mail\Gmail;
use Carbon\Carbon;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {   
        $schedule->job(new DailyReportItemBelowStockJob)->dailyAt('10:00')->onOneServer()->runInBackground();
        $schedule->job(new CalculateDailyOperationalBoatData)->daily()->onOneServer()->runInBackground();
        // $schedule->job(new CalculateDailyOperationalBoatData)->everyFiveMinutes()->onOneServer()->runInBackground();
        // $schedule->job(new SendItemBelowStockReportJob('Jakarta'))->fridays()->at('10:00')->onOneServer()->runInBackground();

        $schedule->job(new SendEmailJob)->monthlyOn(15, '12:00')->onOneServer()->runInBackground();
        // $schedule->command('inspire')->hourly();
        // $now = Carbon::now();
        // $month = $now->format('F');
        // $year = $now->format('yy');

        // $fourthFridayMonthly = new Carbon('fourth friday of ' . $month . ' ' . $year);
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
