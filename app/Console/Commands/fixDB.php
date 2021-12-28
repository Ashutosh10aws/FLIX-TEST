<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\PlayList;
use App\Model\PlayListPricePackage;
use DB;

class fixDB extends Command
{
    protected $signature = 'fix:db';
    protected $description = 'Command description';
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $non_zero_playlists=DB::table('transactions')->where('id','!=', 0)->orderBy('created_at','desc')->get();
        $latest_id=$non_zero_playlists->first()->id;
        $latest_id+=1;
        echo $latest_id;

        $zero_play_lists=DB::table('transactions')->where('id','==', 0)->orderBy('created_at')->get();
        foreach ($zero_play_lists as $key=>$item){
            $updated_id=$latest_id+$key;
            DB::table('transactions')->where('created_at','=',$item->created_at)->update(['id'=>$updated_id]);
        }

        $duplicated_map=[];
        $temps=DB::table('transactions')->where('created_at','>','2020-12-24')->orderBy('created_at')->get();
        foreach ($temps as $item){
            $duplicated_map[$item->id][]=$item;
        }
        foreach ($duplicated_map as $item){
            if(count($item)>1){
                $id=$item[0]->id;
                DB::table('transactions')->where('id','=',$id)->delete();
            }
        }


    }
}
