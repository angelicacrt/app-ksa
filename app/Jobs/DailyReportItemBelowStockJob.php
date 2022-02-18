<?php

namespace App\Jobs;

use App\Mail\DailyItemBelowStockReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Item;
use App\Models\ItemBelowStock;

class DailyReportItemBelowStockJob implements ShouldQueue
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
        // Define all branches
        $branch = ['Jakarta', 'Banjarmasin', 'Samarinda', 'Bunati', 'Babelan', 'Berau', 'Kendari'];

        // Then send the message to all users of respectives branches
        foreach($branch as $b){
            $user = User::where('cabang', $b)->whereHas('roles', function($query){
                $query->where('name', 'logistic')
                    ->orWhere('name', 'supervisor')
                    ->orWhere('name', 'supervisorLogisticMaster');
            })->pluck('email');

            $items_below_stock = ItemBelowStock::join('items', 'item_below_stocks.item_id', '=', 'items.id')->where('cabang', $b)->get();

            Mail::to($user)->send(new DailyItemBelowStockReport($items_below_stock, $b));
        }
    }
}
