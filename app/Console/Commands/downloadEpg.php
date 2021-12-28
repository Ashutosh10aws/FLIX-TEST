<?php

namespace App\Console\Commands;

use App\Model\EpgCode;
use App\Model\EpgData;
use Illuminate\Console\Command;
use App\Model\ChannelList;

class downloadEpg extends Command
{

    protected $signature = 'download:epg';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        $epg_codes=EpgCode::get()->toArray();
        $k=0;
        while($k<=0){
            for($i=count($epg_codes)-1;$i>=0;$i--){
                $item=$epg_codes[$i];
                $record_data=[];
                $channel_records=[];
                try{
                    $url=$item['url'];
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL,$url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                    $headers = array("Content-type:application/json");
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    $data = curl_exec($ch);

                    if(is_null($data) || $data=='')
                    {
                        echo $url."\n";
                        continue;
                    }
                    curl_close($ch);
                    $xml = simplexml_load_string($data);
                    EpgData::where('epg_code_id',$item['id'])->delete();
                    ChannelList::where('epg_code_id',$item['id'])->delete();
                    foreach ($xml as $epg_item){
                        $epg_item = json_decode(json_encode((array)$epg_item), TRUE);
                        if(isset($epg_item['display-name'])){
                            $one_channel_record=[
                                'epg_code_id'=>$item['id'],
                                'channel_id'=>'',
                                'name'=>''
                            ];
                            $one_channel_record['name']=$epg_item['display-name'];
                            if(isset($epg_item['@attributes'])){
                                $one_channel_record['channel_id']=$epg_item['@attributes']['id'];
                            }
                            $channel_records[]=$one_channel_record;
                            if(count($channel_records)>1000){
                                ChannelList::insert($channel_records);
                                $channel_records=[];
                            }
                        }else{
                            $record_one=[
                                'epg_code_id'=>$item['id'],
                                'start'=>'',
                                'stop'=>'',
                                'channel_id'=>'',
                                'title'=>'',
                                'desc'=>'',
                                'category'=>''
                            ];
                            if(isset($epg_item['@attributes'])){
                                $attributes=$epg_item['@attributes'];
                                if(isset($attributes['start']))
                                {
                                    $start=$attributes['start'];
                                    $d=strtotime($start);
                                    $record_one['start']=date("Y-m-d H:i:s", $d);
                                }
                                if(isset($attributes['stop']))
                                {
                                    $stop=$attributes['stop'];
                                    $d=strtotime($stop);
                                    $record_one['stop']=date("Y-m-d H:i:s", $d);
                                }
                                if(isset($attributes['channel']))
                                    $record_one['channel_id']=$attributes['channel'];
                            }
                            if(isset($epg_item['title'])){
                                $title=$epg_item['title'];
                                if(gettype($title)=='string'){
                                    $record_one['title']=$title;
                                }
                                else{
                                    if(isset($title[0]))
                                        $record_one['title']=$title[0];
                                }
                            }
                            if(isset($epg_item['desc'])){
                                $desc=$epg_item['desc'];
                                if(gettype($desc)=='string')
                                    $record_one['desc']=$desc;
                                else if(isset($desc[0]))
                                    $record_one['desc']=$desc[0];
                            }
                            if(isset($epg_item['category'])){
                                $category=$epg_item['category'];
                                if(gettype($category)=='string'){
                                    $record_one['category']=$category;
                                }
                                else if(isset($category[0]))
                                    $record_one['category']=$category[0];
                            }
                            if(count($record_data)>1000){
                                EpgData::insert($record_data);
                                $record_data=[];
                            }
                            $record_data[]=$record_one;
                        }
                    }
                    if(count($record_data)>0)
                        EpgData::insert($record_data);
                    if(count($channel_records)>0)
                        ChannelList::insert($channel_records);
                    array_splice($epg_codes,$i,1);
                }catch(\Exception $e){
                    echo $e->getMessage()."\n";
                    echo $url."\n";
                    echo "xml=\n";
//                    print_r($data);
                    echo "\n";
                }
            }
            if(count($epg_codes)==0)
                break;
            $k++;
            echo "I tried $k times\n";
        }
    }
}
