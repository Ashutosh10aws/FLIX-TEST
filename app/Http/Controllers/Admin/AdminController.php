<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Model\ActivationContent;
use App\Model\Admin;
use App\Model\CoinList;
use App\Model\MyListContent;
use App\Model\PlayList;
use App\Model\PlayListPricePackage;
use App\Model\PlayListUrl;
use App\Model\PrivacyPageContent;
use App\Model\ResellerPricePackage;
use App\Model\TermsPageContent;
use App\Model\Transaction;
use Illuminate\Http\Request;
use App\Traits\SummernoteOperation;
use App\Traits\SettingHelper;
use App\Model\Language;
use App\Model\Word;
use App\Model\LanguageWord;
use DB;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    use SummernoteOperation;
    use SettingHelper;
    public function index(){
        return view('admin.index');
    }
    public function showMyListPageContent(){
        $mylist_content=null;
        if(MyListContent::get()->count()>0)
            $mylist_content=MyListContent::get()->first();
        return view('admin.mylist', compact('mylist_content'));
    }

    public function saveMyListContent(Request $request){
        $detail=$request->input('section-content');
        if(MyListContent::get()->count()>0)
            $mylist_content=MyListContent::get()->first();
        else
            $mylist_content=new MyListContent;
        $detail=$this->changeDomContent($detail);
        $mylist_content->contents=$detail;
        $mylist_content->save();
        return redirect()->back();
    }

    public function showActivationPageContent(){
        $mylist_content=null;
        if(ActivationContent::get()->count()>0)
            $mylist_content=ActivationContent::get()->first();
        return view('admin.activation', compact('mylist_content'));
    }

    public function saveActivationContent(Request $request){
        $detail=$request->input('section-content');
        if(ActivationContent::get()->count()>0)
            $mylist_content=ActivationContent::get()->first();
        else
            $mylist_content=new ActivationContent;
        $detail=$this->changeDomContent($detail);
        $mylist_content->contents=$detail;
        $mylist_content->save();
        return redirect()->back();
    }

    public function showTermsContent(){
        $terms_content=null;
        if(TermsPageContent::get()->count()>0)
            $terms_content=TermsPageContent::get()->first();
        return view('admin.terms', compact('terms_content'));
    }

    public function saveTermsContent(Request $request){
        $detail=$request->input('section-content');
        if(TermsPageContent::get()->count()>0)
            $mylist_content=TermsPageContent::get()->first();
        else
            $mylist_content=new TermsPageContent;
        $detail=$this->changeDomContent($detail);
        $mylist_content->contents=$detail;
        $mylist_content->save();
        return redirect()->back();
    }


    public function showPrivacyContent(){
        $terms_content=null;
        if(PrivacyPageContent::get()->count()>0)
            $terms_content=PrivacyPageContent::get()->first();
        return view('admin.privacy', compact('terms_content'));
    }

    public function savePrivacyContent(Request $request){
        $detail=$request->input('section-content');
        if(PrivacyPageContent::get()->count()>0)
            $mylist_content=PrivacyPageContent::get()->first();
        else
            $mylist_content=new PrivacyPageContent;
        $detail=$this->changeDomContent($detail);
        $mylist_content->contents=$detail;
        $mylist_content->save();
        return redirect()->back();

    }

    public function transactions(){
        return view('admin.transaction');
    }
    public function getTransactions(Request $request){
        $input=$request->all();
        $show_paypal=$input['show_paypal'];
        $show_crypto=$input['show_crypto'];
        $show_card=$input['show_card'];

        $draw = $input['draw'];
        $rowperpage = $input['length'];
        $row = $input['start'];

        $columnIndex = $input['order'][0]['column']; // Column index
        $columnName = $input['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $input['order'][0]['dir']; // asc or desc
        $searchValue = $input['search']['value']; // Search value

        $transactions=DB::table('transactions')->Join('play_lists','transactions.playlist_id','=','play_lists.id')->where('status','success');
        if($show_card==false || $show_card=='false'){
            $transactions=$transactions->where('payment_type','!=','card');
        }

        if($show_paypal==false || $show_paypal=='false'){
            $transactions=$transactions->whereNotIn('payment_type',['paypal','Paypal Test']);
        }

        if($show_crypto==false || $show_crypto=='false'){
            $transactions=$transactions->whereIn('payment_type',['paypal','card','Paypal Test']);
        }

        if(!is_null($searchValue) && $searchValue!='')
            $transactions=$transactions->where(function($query) use ($searchValue){
                return $query->
                where('mac_address','LIKE',"%$searchValue%")->
                orWhere('payment_type','LIKE',"%$searchValue");
            });
        $totalRecords=$transactions->get()->count();
        $transactions=$transactions->select('play_lists.mac_address','play_lists.app_type','transactions.amount','transactions.payment_type','transactions.pay_time')->skip($row)->take($rowperpage);
        if($columnName=='app_type')
            $transactions=$transactions->orderBy('play_lists.app_type',$columnSortOrder);
        if($columnName=='pay_time')
            $transactions=$transactions->orderBy('transactions.pay_time',$columnSortOrder);
        if($columnName=='payment_type')
            $transactions=$transactions->orderBy('transactions.payment_type',$columnSortOrder);
        if($columnName=='amount')
            $transactions=$transactions->orderBy('transactions.amount',$columnSortOrder);

        $transactions=$transactions->get();
        return ['data'=>$transactions,"draw" => intval($draw),"iTotalDisplayRecords" => $totalRecords,
            "iTotalRecords" => $totalRecords,'inputs'=>$input];
    }

    public function showSeoSetting(){
        $news_meta_content=$this->getSetting('news_meta_content');
        $support_meta_content=$this->getSetting('support_meta_content');
        $instruction_meta_content=$this->getSetting('instruction_meta_content');
        $mylist_meta_content=$this->getSetting('mylist_meta_content');
        $activation_meta_content=$this->getSetting('activation_meta_content');
        $terms_meta_content=$this->getSetting('terms_meta_content');
        $privacy_meta_content=$this->getSetting('privacy_meta_content');

        $news_meta_title=$this->getSetting('news_meta_title');
        $support_meta_title=$this->getSetting('support_meta_title');
        $instruction_meta_title=$this->getSetting('instruction_meta_title');
        $mylist_meta_title=$this->getSetting('mylist_meta_title');
        $activation_meta_title=$this->getSetting('activation_meta_title');
        $terms_meta_title=$this->getSetting('terms_meta_title');
        $privacy_meta_title=$this->getSetting('privacy_meta_title');

        $news_meta_keyword=$this->getSetting('news_meta_keyword');
        $support_meta_keyword=$this->getSetting('support_meta_keyword');
        $instruction_meta_keyword=$this->getSetting('instruction_meta_keyword');
        $mylist_meta_keyword=$this->getSetting('mylist_meta_keyword');
        $activation_meta_keyword=$this->getSetting('activation_meta_keyword');
        $terms_meta_keyword=$this->getSetting('terms_meta_keyword');
        $privacy_meta_keyword=$this->getSetting('privacy_meta_keyword');

        return view('admin.seo_setting',
            compact('news_meta_content','instruction_meta_content','support_meta_content','mylist_meta_content',
                'activation_meta_content','terms_meta_content','privacy_meta_content',
                'news_meta_title','activation_meta_title','instruction_meta_title','support_meta_title','terms_meta_title',
                'privacy_meta_title','mylist_meta_title',
                'news_meta_keyword','support_meta_keyword','instruction_meta_keyword','mylist_meta_keyword','activation_meta_keyword',
                'terms_meta_keyword','privacy_meta_keyword'
            )
        );
    }

    public function saveSeoSetting(Request $request){
        $input=$request->all();
        $news_meta_content=$input['news_meta_content'];
        $support_meta_content=$input['support_meta_content'];
        $instruction_meta_content=$input['instruction_meta_content'];
        $mylist_meta_content=$input['mylist_meta_content'];
        $activation_meta_content=$input['activation_meta_content'];
        $terms_meta_content=$input['terms_meta_content'];
        $privacy_meta_content=$input['privacy_meta_content'];

        $news_meta_title=$input['news_meta_title'];
        $support_meta_title=$input['support_meta_title'];
        $instruction_meta_title=$input['instruction_meta_title'];
        $mylist_meta_title=$input['mylist_meta_title'];
        $activation_meta_title=$input['activation_meta_title'];
        $terms_meta_title=$input['terms_meta_title'];
        $privacy_meta_title=$input['privacy_meta_title'];

        $news_meta_keyword=$input['news_meta_keyword'];
        $support_meta_keyword=$input['support_meta_keyword'];
        $instruction_meta_keyword=$input['instruction_meta_keyword'];
        $mylist_meta_keyword=$input['mylist_meta_keyword'];
        $activation_meta_keyword=$input['activation_meta_keyword'];
        $terms_meta_keyword=$input['terms_meta_keyword'];
        $privacy_meta_keyword=$input['privacy_meta_keyword'];

        $this->saveSetting('news_meta_content',$news_meta_content);
        $this->saveSetting('support_meta_content',$support_meta_content);
        $this->saveSetting('instruction_meta_content',$instruction_meta_content);
        $this->saveSetting('mylist_meta_content',$mylist_meta_content);
        $this->saveSetting('activation_meta_content',$activation_meta_content);
        $this->saveSetting('terms_meta_content',$terms_meta_content);
        $this->saveSetting('privacy_meta_content', $privacy_meta_content);

        $this->saveSetting('news_meta_title',$news_meta_title);
        $this->saveSetting('support_meta_title',$support_meta_title);
        $this->saveSetting('instruction_meta_title',$instruction_meta_title);
        $this->saveSetting('mylist_meta_title',$mylist_meta_title);
        $this->saveSetting('activation_meta_title',$activation_meta_title);
        $this->saveSetting('terms_meta_title',$terms_meta_title);
        $this->saveSetting('privacy_meta_title',$privacy_meta_title);

        $this->saveSetting('news_meta_keyword',$news_meta_keyword);
        $this->saveSetting('support_meta_keyword',$support_meta_keyword);
        $this->saveSetting('instruction_meta_keyword',$instruction_meta_keyword);
        $this->saveSetting('mylist_meta_keyword',$mylist_meta_keyword);
        $this->saveSetting('activation_meta_keyword',$activation_meta_keyword);
        $this->saveSetting('terms_meta_keyword',$terms_meta_keyword);
        $this->saveSetting('privacy_meta_keyword',$privacy_meta_keyword);
        return redirect()->back()->with('message','Seo Settings saved successfully');
    }

    public function showDemoUrl(){
        $demo_url=$this->getSetting('demo_url');
        return view('admin.demo_url', compact('demo_url'));
    }

    public function saveDemoUrl(Request $request){
        $demo_url=$request->input('demo_url');
        $this->saveSetting('demo_url', $demo_url);
        return redirect()->back();
    }

    public function showAppBackground(){
        $themes=$this->getSetting('themes');
        if($themes!="")
        {
            $themes=json_decode($themes);
            for($i=0;$i<count($themes);$i++){
                if($themes[$i]->url==''){
                    $themes[$i]->url='https://dummyimage.com/1920x1080/fff/aaa';
                }
                $themes[$i]->origin_url=$themes[$i]->url;
            }
        }
        else{
            $themes=[
                [
                    'theme_name'=>'',
                    'url'=>'https://dummyimage.com/1920x1080/fff/aaa',
                    'origin_url'=>'https://dummyimage.com/1920x1080/fff/aaa'
                ]
            ];
        }
        $themes=json_encode($themes);
        return view('admin.app_background', compact('themes'));
    }

    public function saveThemes(Request $request){
        $input=$request->all();
        $theme_count=$input['theme_count'];
        $theme_contents=[];

        for($i=0;$i<$theme_count;$i++){
            $theme_contents[$i]['name']=$input['theme-name-'.$i];
            $theme_contents[$i]['url']=$input['theme-origin_url-'.$i];
            if($request->hasFile('theme-image-'.$i)){
                $file=$request->file('theme-image-'.$i);
                $file_name=time().".".$file->getClientOriginalExtension();
                $file->move(public_path('/upload'),$file_name);
                $theme_contents[$i]['url']='/upload/'.$file_name;
            }
        }
        $this->saveSetting('themes',json_encode($theme_contents));
        return redirect()->back();
    }

    public function showAdverts(){
        $adverts=$this->getSetting('adverts');
        if($adverts!="")
        {
            $adverts=json_decode($adverts);
            for($i=0;$i<count($adverts);$i++){
                if($adverts[$i]->url==''){
                    $adverts[$i]->url='https://dummyimage.com/500x300/fff/aaa';
                }
                $adverts[$i]->origin_url=$adverts[$i]->url;
            }
        }
        else{
            $adverts=[
                [
                    'title'=>'',
                    'description'=>'',
                    'url'=>'https://dummyimage.com/500x300/fff/aaa',
                    'origin_url'=>'https://dummyimage.com/500x300/fff/aaa'
                ]
            ];
        }
        $adverts=json_encode($adverts);
        return view('admin.advert', compact('adverts'));
    }

    public function saveAdverts(Request $request){
        $input=$request->all();
        $advert_count=$input['advert_count'];
        $advert_contents=[];

        for($i=0;$i<$advert_count;$i++){
            $advert_contents[$i]['title']=$input['advert-title-'.$i];
            $advert_contents[$i]['description']=$input['advert-description-'.$i];
            $advert_contents[$i]['url']=$input['advert-origin_url-'.$i];
            if($request->hasFile('advert-image-'.$i)){
                $file=$request->file('advert-image-'.$i);
                $file_name=time().".".$file->getClientOriginalExtension();
                $file->move(public_path('/upload'),$file_name);
                $advert_contents[$i]['url']='/upload/'.$file_name;
            }
        }
        $this->saveSetting('adverts',json_encode($advert_contents));
        return redirect()->back();
    }

    public function showStripeSetting(){
        $stripe_public_key=$this->getSetting('stripe_public_key');
        $stripe_secret_key=$this->getSetting('stripe_secret_key');
        return view('admin.stripe_setting', compact('stripe_public_key','stripe_secret_key'));
    }

    public function saveStripeSetting(Request $request){
        $input=$request->all();
        $public_key=$input['public_key'];
        $secret_key=$input['secret_key'];
        $this->saveSetting('stripe_public_key',$public_key);
        $this->saveSetting('stripe_secret_key',$secret_key);
        return redirect()->back();
    }


    public function showPaypalSetting(){
        $paypal_client_id=$this->getSetting('paypal_client_id');
        $paypal_secret=$this->getSetting('paypal_secret');
        $paypal_mode=$this->getSetting('paypal_mode');
        return view('admin.paypal_setting', compact('paypal_client_id','paypal_secret','paypal_mode'));
    }

    public function savePaypalSetting(Request $request){
        $input=$request->all();
        $paypal_client_id=$input['paypal_client_id'];
        $paypal_secret=$input['paypal_secret'];
        $paypal_mode=$input['paypal_mode'];
        $this->saveSetting('paypal_client_id',$paypal_client_id);
        $this->saveSetting('paypal_secret',$paypal_secret);
        $this->saveSetting('paypal_mode',$paypal_mode);
        return redirect('admin/showPaypalSetting');
    }

    public function showCryptoApiKey(){
        $crypto_public_key=$this->getSetting('crypto_public_key');
        $crypto_private_key=$this->getSetting('crypto_private_key');
        return view('admin.crypto_key', compact('crypto_public_key','crypto_private_key'));
    }

    public function saveCryptoApiKey(Request $request){
        $input=$request->all();
        $crypto_public_key=$input['crypto_public_key'];
        $crypto_private_key=$input['crypto_private_key'];
        $this->saveSetting('crypto_public_key',$crypto_public_key);
        $this->saveSetting('crypto_private_key',$crypto_private_key);
        return redirect()->back();
    }

    public function showCoinList(Request $request){
        $coin_list=null;
        if(CoinList::get()->first()){
            $coin_list=CoinList::get()->first();
        }
        return view('admin.coin_list',compact('coin_list'));
    }

    public function saveCoinList(Request $request){
        $input=$request->all();
        $codes=$input['codes'];
        $names=$input['names'];
        if(CoinList::get()->first())
            $coin_list=CoinList::get()->first();
        else
            $coin_list=new CoinList;
        $data=[];
        for($i=0;$i<count($codes);$i++){
            $data[]=[
                'code'=>$codes[$i],
                'name'=>$names[$i]
            ];
        }
        $coin_list->data=$data;
        $coin_list->save();
        return redirect()->back();
    }

    public function showAndroidUpdate(){
        $android_version_code=$this->getSetting('android_version_code');
        $apk_url=$this->getSetting('apk_url');
        return view('admin.android_update', compact('android_version_code','apk_url'));
    }

    public function saveAndroidUpdate(Request $request){
        $android_version_code=$request->input('android_version_code');
        $file=$request->file('apk_file');
        $file_name='android_'.$android_version_code.'.apk';
        $file->move(public_path('/upload'),$file_name);
        $this->saveSetting('android_version_code',$android_version_code);
        $this->saveSetting('apk_url',url('/upload/'.$file_name));
        return redirect()->back()->with('message','Android version updated successfully');
    }

    public function showLanguage(){
        $languages=Language::get();
        return view('admin.language', compact('languages'));
    }
    public function createLanguage(Request $request){
        $input=$request->all();
        $language_code=$input['language_code'];
        $language_name=$input['language_name'];
        $id=$input['id'];
        if(is_null($id) || $id=='')
            $language=new Language;
        else
            $language=Language::find($id);
        $language->code=$language_code;
        $language->name=$language_name;
        $language->save();
        return $language;
    }
    public function deleteLanguage($id){
        Language::destroy($id);
        return ['status'=>'success'];
    }

    public function showWord(){
        $words=Word::get();
        return view('admin.word',compact('words'));
    }
    public function createWord(Request $request){
        $input=$request->all();
        $name=$input['word_name'];
        $id=$input['id'];
        if(is_null($id) || $id=='')
            $word=new Word;
        else
            $word=Word::find($id);
        $word->name=$name;
        $word->save();
        return $word;
    }
    public function deleteWord($id){
        Word::destroy($id);
        return ['status'=>'success'];
    }

    public function showLanguageWord($language_id){
        $words=Word::get();
        $language=Language::find($language_id);
        $language_words=LanguageWord::where('language_id',$language_id)->get();
        $language_words_map=[];
        foreach ($language_words as $item){
            $language_words_map[strval($item->word_id)]=$item;
        }
        return view('admin.language_word',compact('words','language_words','language_id','language_words_map','language'));
    }

    public function saveLanguageWord(Request $request,$language_id){
        $input=$request->all();
        LanguageWord::where('language_id',$language_id)->delete();
        $now=(new \DateTime())->format('Y-m-d H:i:s');
        $records=[];
        foreach ($input as $key=>$item){
            if(strpos($key,'language_word')!==false){
                $word_id=str_replace('language_word-',"",$key);
                if(!is_null($item) && $item!=''){
                    $records[]=[
                        'word_id'=>$word_id,
                        'language_id'=>$language_id,
                        'value'=>$item,
                        'created_at'=>$now,
                        'updated_at'=>$now
                    ];
                }

            }
        }
        if(count($records)>0)
            LanguageWord::insert($records);
        return redirect()->back()->with('message','LanguageWords saved successfully');
    }

    public function saveLanguageFile(Request $request,$language_id){
        if($request->hasFile('language-file')){
            $file=$request->file('language-file');
            $temp=file_get_contents($file->getRealPath());
            $xml = simplexml_load_string($temp);
            echo "<pre>";
            foreach ($xml as $item){
                $item = json_decode(json_encode((array)$item), TRUE);
                $attr=$item['@attributes'];
                $word_name=$attr['name'];
                if(Word::where('name',$word_name)->get()->count()>0){
                    $word=Word::where('name',$word_name)->get()->first();
                }else{
                    $word=new Word;
                    $word->name=$word_name;
                    $word->save();
                }
                $language_value=$item[0];
                $temps=LanguageWord::where([['word_id','=',$word->id],['language_id','=',$language_id]])->get();
                if($temps->count()>0)
                    $language_word=$temps->first();
                else
                {
                    $language_word=new LanguageWord;
                    $language_word->language_id=$language_id;
                    $language_word->word_id=$word->id;
                }
                $language_word->value=$language_value;
                $language_word->save();
            }
            return redirect()->back()->with('message','Words saved successfully');
        }else{
            echo "no file";
        }
    }


    public function showMollieSetting(){
        $api_key=$this->getSetting('mollie_api_key');
        return view('admin.mollie_setting', compact('api_key'));
    }

    public function saveMollieSetting(Request $request){
        $input=$request->all();
        $api_key=$input['api_key'];
        $this->saveSetting('mollie_api_key',$api_key);
        return redirect('admin/showMollieSetting');
    }

    public function showPaymentVisibility(){
        $show_paypal=$this->getSetting('show_paypal');
        $show_coin=$this->getSetting('show_coin');
        $show_stripe=$this->getSetting('show_stripe');
        if($show_paypal=='')
            $show_paypal=1;
        if($show_coin=='')
            $show_coin=1;
        return view('admin.payment_visibility',compact('show_paypal','show_coin','show_stripe'));
    }
    public function savePaymentVisibility(Request $request){
        $input=$request->all();
        $show_paypal=$input['show_paypal'];
        $show_coin=$input['show_coin'];
        $show_stripe=$input['show_stripe'];
        $this->saveSetting('show_paypal',$show_paypal);
        $this->saveSetting('show_coin',$show_coin);
        $this->saveSetting('show_stripe',$show_stripe);
        return redirect('admin/showPaymentVisibility');
    }


    public function showResellers(){
        $resellers=Admin::where('is_admin',0)->get();
        return view('admin.reseller', compact('resellers'));
    }

    public function createReseller(Request $request){
        $input=$request->all();
        $max_connections=$input['max_connections'];
        $email=$input['email'];
        $reseller_id=$input['reseller_id'];
        if(Admin::where('email',$email)->get()->first()){
            if($reseller_id==-1){
                return [
                    'status'=>'error',
                    'msg'=>'Sorry, the email of reseller already exist'
                ];
            }else{
                if(Admin::where('email',$email)->get()->first()->id!=$reseller_id){
                    return [
                        'status'=>'error',
                        'msg'=>'Sorry, the email of reseller already exist'
                    ];
                }
            }
        }
        if($reseller_id==-1)
        {
            $reseller=new Admin;
            $password=bcrypt($input['password']);
            $reseller->password=$password;
        }
        else
        {
            $reseller=Admin::find($reseller_id);
            if($input['password']!=''){
                $password=bcrypt($input['password']);
                $reseller->password=$password;
            }
        }
        $reseller->email=$email;
        $reseller->max_connections=$max_connections;
        $reseller->is_admin=0;
        $reseller->save();
        return [
            'status'=>'success',
            'id'=>$reseller->id,
            'active_connections'=>$reseller->used_connections
        ];
    }

    public function deleteReseller(Request $request){
        $input=$request->all();
        $reseller_id=$input['reseller_id'];
        Admin::destroy($reseller_id);
        return [
            'status'=>'success'
        ];
    }

    public function showProfile(){
        $user=Auth::guard('admin')->user();
        return view('admin.profile',compact('user'));
    }

    public function updateProfile(Request $request){
        $input=$request->all();
        $email=$input['email'];
        $password=$input['password'];
        $user=Auth::guard('admin')->user();
        $user->email=$email;
        if($password!=''){
            $user->password=bcrypt($password);
        }
        $user->save();
        return redirect()->back();
    }

    public function showRemoveOldDevice(){
        $total_device_count=PlayList::count();
        $old_date=(new \DateTime())->modify('-2 months')->format('Y-m-d');
        $old_device_count=PlayList::where([['expire_date','<=',$old_date],['is_trial','!=',2]])->count();
        return view('admin.remove_device',compact('total_device_count','old_device_count'));
    }

    public function postRemoveDevice(){
        \Artisan::call('remove:device');
        return redirect()->back();
    }
}


