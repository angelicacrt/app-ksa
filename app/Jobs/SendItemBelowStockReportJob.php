<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\WeeklyItemBelowStockReportMail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Item;

class SendItemBelowStockReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $item_id;
    public $branch;

    public function __construct($item_id, $branch)
    {   
        $this->item_id = $item_id;
        $this->branch = $branch;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {   
        $items = Item::where('id', $this->item_id)->get();

        $users = User::where('cabang', $this->branch)->whereHas('roles', function($query){
            $query->where('name', 'logistic')
                ->orWhere('name', 'supervisor')
                ->orWhere('name', 'supervisorLogisticMaster');
        })->get();

        if(count($users) == 0){
            return;
        }

        foreach($users as $user){
            Mail::to($user -> email)->send(new WeeklyItemBelowStockReportMail($items, $this->branch));
        }
    }
}
