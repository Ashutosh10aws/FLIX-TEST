<?php


namespace App\Traits;
use App\Model\Setting;

trait SettingHelper

{
    public function getSetting($key){
        $value='';
        if(Setting::where('key',$key)->get()->count()>0)
        {
            $value=Setting::where('key',$key)->get()->first()->value;
        }
        return $value;
    }

    public function saveSetting($key, $value){
        if(Setting::where('key',$key)->get()->count()>0)
            $setting=Setting::where('key',$key)->get()->first();
        else
            $setting=new Setting;
        $setting->key=$key;
        $setting->value=$value;
        $setting->save();
    }

}
