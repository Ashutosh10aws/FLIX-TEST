<?php

namespace App\Http\Controllers;

use App\Model\EpgData;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Model\PlayList;
use App\Model\PlayListUrl;
use App\Traits\SettingHelper;
use DB;
use App\Model\Language;
use App\Model\Word;
use App\Model\LanguageWord;
use App\Model\PlayListPricePackage;
use App\Model\Transaction;
use App\Model\EpgCode;
use App\Model\EpgServer;



class ApiController extends Controller
{
    use SettingHelper;
    public function getPlayListInformation($mac_address){
        $start_time1=microtime(true);
        $result=Array();
        $start_time=microtime(true);
        $temps=PlayList::where('mac_address',$mac_address)->get();
        $end_time=microtime(true);
        $result['playlist getting time']=$end_time-$start_time;
        if($temps->first()){
            $play_list=$temps->first();
            $result['mac_registered']=true;
            $result['is_trial']=$play_list->is_trial;

            if($play_list->is_trial==0){  // if user did not try trial plan
                $date_obj=new \DateTime();
                $play_list->expire_date=$date_obj->modify('+7 days')->format('Y-m-d');
                $play_list->is_trial=1;
                $result['is_trial']=1;
                $play_list->save();
            }
            $result['expire_date']=$play_list->expire_date;
            foreach ($play_list->PlayListUrls as $playListUrl){
                $result['urls'][]=$playListUrl->url;
            }
            if(count($play_list->PlayListUrls)==0){
                $result['urls'][]=$this->getSetting('demo_url');
            }
        }
        else{
            $result['mac_registered']=false;
            $result['urls'][]=$this->getSetting('demo_url');
        }

        $themes=$this->getSetting('themes');
        $themes=$themes=="" ? [] : json_decode($themes);
        foreach ($themes as $theme){
            if(!strpos($theme->url,"http"))
                $theme->url=url('public'.$theme->url);
        }
        $result['themes']=$themes;
        $android_version_code=$this->getSetting('android_version_code');
        $apk_url=$this->getSetting('apk_url');
        $result['android_version_code']=$android_version_code;
        $result['apk_url']=$apk_url;

        $temps=EpgServer::get();
        foreach ($temps as $temp){
            $result['epg_urls'][]=$temp->url;
        }

        $start_time=microtime(true);
        $words=Word::get();
        $end_time=microtime(true);
        $result['word getting time']=$end_time-$start_time;
        $words_map=[];
        foreach ($words as $word){
            $words_map[strval($word->id)]=$word->name;
        }
        $languages=Language::select('id','code','name')->orderBy('name')->get();
        $language_words=LanguageWord::select('language_id','word_id','value')->get();
        $language_word_map=[];
        foreach ($language_words as $item){
            $language_word_map[strval($item->language_id)][]=$item;
        }
        foreach ($languages as $item){
            if(isset($language_word_map[strval($item->id)])){
                $temps=$language_word_map[strval($item->id)];
                $temps_map=[];
                foreach ($temps as $temp){
                    $word_key=$words_map[strval($temp->word_id)];
                    $temps_map[$word_key]=$temp->value;
                }
                $item->words=$temps_map;
            }else{
                $item->words=[];
            }
        }
        $result['languages']=$languages;
        file_put_contents(storage_path('logs/laravel.log'),'');
        $end_time1=microtime(true);
        $result['total_execution_time']=$end_time1-$start_time1;
        return response()->json($result);
    }

    public function getAppSetting(Request $request,$mac_address,$app_type=null){
        $start_time1=microtime(true);
        $temps=PlayList::where('mac_address',$mac_address)->get();
        $user_agent=strtolower($request->header('User-Agent'));
        $result['aaa']=$app_type;
        if(is_null($app_type)){
            if(strpos($user_agent, 'tizen') !== false)
                $app_type='samsung';
            if(strpos($user_agent, 'web0s') !== false)
                $app_type='lg';
            if(strpos($user_agent, 'android') !== false)
                $app_type='android';
            if(strpos($user_agent, 'ipod') !== false || stripos($user_agent, 'iphone') !== false || stripos($user_agent, 'ipad') !== false)
                $app_type='ios';
        }
        if(!$temps->first()){
            $playlist=new PlayList;
            $today=new \DateTime();
            $playlist->expire_date=$today->modify('+7 days')->format('Y-m-d');
            $playlist->mac_address=$mac_address;
            $playlist->is_trial=1;
            $playlist->app_type=$app_type;
            $playlist->save();
        }
        else{
            $playlist=$temps->first();
            $playlist->app_type=$app_type;
            $playlist->save();
        }
        $result=Array();
        $themes=$this->getSetting('themes');
        $themes=$themes=="" ? [] : json_decode($themes);
        foreach ($themes as $theme){
            if(!strpos($theme->url,"http"))
                $theme->url=url('public'.$theme->url);
        }
        $result['themes']=$themes;

        $adverts=$this->getSetting('adverts');
        $adverts=$adverts=="" ? [] : json_decode($adverts);
        foreach ($adverts as $advert){
            if(!strpos($advert->url,"http"))
                $advert->url=url('public'.$advert->url);
        }
        $result['adverts']=$adverts;
        $result['app_type']=$app_type;
        $result['use_agent']=$user_agent;
        $end_time1=microtime(true);
        $result['total_execution_time']=$end_time1-$start_time1;
        return response()->json($result);
    }

    public function getEpgData(Request $request,$offset_minute=null){
        return [];
        if(is_null($offset_minute))
            $offset_minute=0;
        if($offset_minute>=0)
            $today=(new \DateTime())->add(new \DateInterval('PT' . $offset_minute . 'M'));
        else
            $today=(new \DateTime())->sub(new \DateInterval('PT' . abs($offset_minute) . 'M'));

        $before_day=$today->sub(new \DateInterval('P02D'))->format('Y-m-d');
        $later_day=$today->add(new \DateInterval('P04D'))->format('Y-m-d');
        $channel_ids=$request->input('channel_ids');
        if(is_null($channel_ids))
            $channel_ids=[];
        $result=[];
        $channel_ids=array_map('strtolower', $channel_ids);
        $temps=EpgData::where([['start','>=',$before_day],['stop','<=',$later_day]])->whereIn(DB::raw('LCASE(channel_id)'),array_map('strtolower', $channel_ids))->select('start','stop','channel_id','title','desc')->get();
        $temps_map=[];
        foreach ($temps as $item){
            $temps_map[strval($item->channel_id)][]=$item;
        }
        foreach ($channel_ids as $channel_id){
            $channel_id=strval($channel_id);
            if(isset($temps_map[$channel_id])){
                $programmes=$temps_map[$channel_id];
                foreach($programmes as $programme){
                    $date_obj=new \DateTime($programme->start);
                    if($offset_minute>=0)
                        $programme->start=$date_obj->add(new \DateInterval('PT' . $offset_minute . 'M'))->format('Y-m-d H:i:s');
                    else
                        $programme->start=$date_obj->sub(new \DateInterval('PT' . abs($offset_minute) . 'M'))->format('Y-m-d H:i:s');

                    $date_obj=new \DateTime($programme->stop);
                    if($offset_minute>=0)
                        $programme->stop=$date_obj->add(new \DateInterval('PT' . $offset_minute . 'M'))->format('Y-m-d H:i:s');
                    else
                        $programme->stop=$date_obj->sub(new \DateInterval('PT' . abs($offset_minute) . 'M'))->format('Y-m-d H:i:s');
                }
                $result[$channel_id]=$programmes;
            }
            else{
                $result[$channel_id]=[];
            }
        }
        return response()->json($result);
    }

    public function getAndroidVersion(){
        $android_version_code=$this->getSetting('android_version_code');
        $apk_url=$this->getSetting('apk_url');
        return [
            'version_code'=>$android_version_code,
            'apk_url'=>$apk_url
        ];
    }

    public function saveAppPurchase(Request $request){
        $input=$request->all();
        $mac_address=$input['mac_address'];
        $amount=$input['amount'];

        if(PlayList::where('mac_address',$mac_address)->get()->count()==0)
            return [
                'status'=>'error',
                'msg'=>'Sorry, mac address does not exist'
            ];
        $price_package=PlayListPricePackage::get()->first();
        $expire_date=((new \DateTime())->modify("+".$price_package->duration.' months'))->format('Y-m-d');
        $today=new \DateTime();

        $playlist=PlayList::where('mac_address',$mac_address)->get()->first();
        $transaction=new Transaction;
        $transaction->playlist_id=$playlist->id;
        $transaction->amount=$amount;
        $transaction->pay_time=$today->format("Y-m-d H:i");
        $transaction->status="success";
        $transaction->payment_type='app_purchase';
        $transaction->save();

        $playlist->expire_date=$expire_date;
        $playlist->is_trial=2;
        $playlist->save();
        return [
            'status'=>'success',
            'expire_date'=>$expire_date
        ];
    }

    public function saveGooglePay(Request $request){
        $input=$request->all();
        $mac_address=$input['mac_address'];
        $amount=$input['amount'];

        if(PlayList::where('mac_address',$mac_address)->get()->count()==0)
            return [
                'status'=>'error',
                'msg'=>'Sorry, mac address does not exist'
            ];
        $price_package=PlayListPricePackage::get()->first();
        $expire_date=((new \DateTime())->modify("+".$price_package->duration.' months'))->format('Y-m-d');
        $today=new \DateTime();

        $playlist=PlayList::where('mac_address',$mac_address)->get()->first();
        $transaction=new Transaction;
        $transaction->playlist_id=$playlist->id;
        $transaction->amount=$amount;
        $transaction->pay_time=$today->format("Y-m-d H:i");
        $transaction->status="success";
        $transaction->payment_type='google_pay';
        $transaction->save();

        $playlist->expire_date=$expire_date;
        $playlist->is_trial=2;
        $playlist->save();
        return [
            'status'=>'success',
            'expire_date'=>$expire_date
        ];
    }

    public function getEpgCode(){
        $epg_codes=EpgCode::get()->toArray();
        return response()->json($epg_codes);
    }



}
