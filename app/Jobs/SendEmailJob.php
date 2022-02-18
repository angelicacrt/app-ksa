<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\Gmail;

class SendEmailJob implements ShouldQueue
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
        $branch = ['Jakarta', 'Banjarmasin', 'Samarinda', 'Bunati', 'Babelan', 'Berau', 'Kendari' , 'Morosi'];

        // Then send the message to all users of respectives branches
        foreach($branch as $b){
            $user = User::where('cabang', $b)->whereHas('roles', function($query){
                $query->where('name', 'picAdmin')
                    ->orWhere('name', 'picSite');
            })->pluck('email');

            $details = [
                'title' => 'Reminder for UPLOADING DOCUMENTS RPK/FUND REQ',
                'body' => 'You will receive This Email Every 15th of the month , confirm that all file is in order 
                and notify the Admin Jakarta if There is Any conflict'
            ];
            Mail::to($user)->send(new Gmail($details));
        }
    }
}
