<?php

namespace App\Console\Commands;

use App\Model\PlayListUrl;
use Illuminate\Console\Command;
use App\Model\PlayList;

class RemoveOldDevice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:device';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $old_date=(new \DateTime())->modify('-2 months')->format('Y-m-d');
        echo "<pre/>";
        echo "old date=".$old_date;
        echo "\n";
        $old_date=(new \DateTime())->modify('-2 months')->format('Y-m-d');
        $old_devices=PlayList::where([['expire_date','<=',$old_date],['is_trial','!=',2]])->orderBy('expire_date','desc')->get();
        $old_device_ids=[];
        $k=0;
        $u=0;
//        foreach ($old_devices as $item){
//            $old_device_ids[]=$item->id;
//            if($k>=1000){
//                $u++;
//                PlayListUrl::whereIn('playlist_id',$old_device_ids)->delete();
//                $k=0;
//                $old_device_ids=[];
//                echo $u."\n";
//            }
//            $k++;
//        }
//        echo "Here Ended";
        PlayList::where([['expire_date','<=',$old_date],['is_trial','!=',2]])->delete();
    }
}
