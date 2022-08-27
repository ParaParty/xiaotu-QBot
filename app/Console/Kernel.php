<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //每日 60s读懂世界
        $schedule->command('Message:EveryDayNews')->dailyAt('7:00');

        //助手 60s读懂世界
        $schedule->command('Assistant:EveryDayNews')->dailyAt('8:00');

        //助手 问候
        $schedule->command('Assistant:Greet')->everyMinute();

        //助手 每日天气
        $schedule->command('Assistant:Weather')->dailyAt('6:58');

        //助手 每日天气
        $schedule->command('Assistant:Ncov')->dailyAt('7:15');
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

    /**
     * Get the timezone that should be used by default for scheduled events.
     *
     * @return \DateTimeZone|string|null
     */
    protected function scheduleTimezone()
    {
        return 'Asia/Shanghai';
    }
}
