<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use DB;

class UpdateStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'postTask:updateStatus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'postTask updateStatus when expired';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
         
        $d =  \Carbon\Carbon::parse(date('Y-m-d'))->format('Y-m-d');

        $t = DB::table('post_tasks')
                      ->where(DB::raw("STR_TO_DATE(date_required,'%d-%m-%Y')"),'<',$d )
                      ->where('task_status','open')
            ->update(['task_status'=>'expired']);
 

        $this->info('current time zone' .config('app.timezone') .date('m-d-Y H:i:s A').' and status updated successfully on '.$d ." and total count= ".$t);

    }
}
