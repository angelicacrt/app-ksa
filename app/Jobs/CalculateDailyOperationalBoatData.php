<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\OperationalBoatData;

class CalculateDailyOperationalBoatData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {   
        // 1) Initialize All The Conditions
        $conditions = ['On Sailing', 'Loading Activity', 'Discharge Activity', 'Standby', 'Repair', 'Docking', 'Standby Docking', 'Grounded Barge', 'Waiting Schedule'];

        $fields_to_be_incremented = ['onSailingDays', 'loadingActivityDays', 'dischargeActivityDays', 'standbyDays', 'repairDays', 'dockingDays', 'standbyDockingDays', 'groundedBargeDays', 'waitingScheduleDays'];

        // 2) Loop Through All The Conditions & Increment The Days Of The Following Conditions (only ongoing task that is going to be incremented)
        for($i = 0; $i < 8; $i ++){
            OperationalBoatData::where('status', 'On Going')->where('condition', $conditions[$i])->increment($fields_to_be_incremented[$i], 1);
        };
    }
}
