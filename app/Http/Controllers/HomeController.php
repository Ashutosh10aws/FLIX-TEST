<?php

namespace App\Http\Controllers;
require_once 'signature.php';

use App\Model\ActivationContent;
use App\Model\CoinList;
use App\Model\CountryCode;
use App\Model\EpgCode;
use App\Model\EpgData;
use App\Model\Faq;
use App\Model\Instruction;
use App\Model\InstructionTag;
use App\Model\MyListContent;
use App\Model\PlayList;
use App\Model\PlayListPricePackage;
use App\Model\PlayListUrl;
use App\Model\PrivacyPageContent;
use App\Model\TermsPageContent;
use App\Model\Transaction;
use App\Model\ChannelList;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Model\News;
use App\Traits\SettingHelper;
use Illuminate\Support\Facades\Log;

use Stripe\Error\Card;
use Cartalyst\Stripe\Stripe;
use Cartalyst\Stripe\Exception\CardErrorException;

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Payer;
use PayPal\Api\Item;
use PayPal\Api\Amount;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Payment;
use PayPal\Api\ItemList;
use PayPal\Api\PaymentExecution;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

require_once(dirname(__FILE__) . '/paymentwall/lib/paymentwall.php');

use Paymentwall_Response_Abstract;
use Paymentwall_Response_Error;
use Paymentwall_Response_Factory;
use Paymentwall_Response_Interface;
use Paymentwall_Response_Success;

use Paymentwall_Signature_Abstract;
use Paymentwall_Signature_Pingback;
use Paymentwall_Signature_Widget;

use Paymentwall_Base;
use Paymentwall_Card;
use Paymentwall_Charge;
use Paymentwall_Config;
use Paymentwall_GenerericApiObject;
use Paymentwall_HttpAction;
use Paymentwall_Instance;
use Paymentwall_Mobiamo;
use Paymentwall_OneTimeToken;
use Paymentwall_Pingback;
use Paymentwall_Product;
use Paymentwall_Subscription;
use Paymentwall_Widget;
define ("PRIVATE_KEY", dirname(__FILE__) ."/key/gpwebpay-pvk.key"); //cesta k soukromemu klici obchodnika / path to merchant private key
define ("PRIVATE_KEY_PSW", "Muhammed5858"); //heslo k soukromemu klici obchodnika / password for merchant private key
define ("PUBLIC_KEY", dirname(__FILE__) ."/key/gpwebpay-pvk.key");

class HomeController extends Controller
{
    use SettingHelper;
    public function __construct()
    {
        $client_id=$this->getSetting('paypal_client_id');
        $secret=$this->getSetting('paypal_secret');
        $paypal_mode=$this->getSetting('paypal_mode');
        $this->_api_context = new ApiContext(new OAuthTokenCredential(
                $client_id,
                $secret
            )
        );
        $this->_api_context->setConfig([
            'mode' => $paypal_mode,
            'http.ConnectionTimeOut' => 30,
            'log.LogEnabled' => true,
            'log.FileName' => public_path('logs/paypal.log'),
            'log.LogLevel' => 'ERROR'
        ]);

        $secret_key=$this->getSetting('stripe_secret_key');
        \Stripe\Stripe::setApiKey($secret_key);
    }

    public function getStripe(){
        $secret_key=$this->getSetting('stripe_secret_key');
        $stripe=Stripe::make($secret_key);
        return $stripe;
    }

    public function index()
    {
        return view('home');
    }

    public function news(){
        $news_sections=News::where('status','publish')->orderBy('id','desc')->get();
        $title=$this->getSetting('news_meta_title');
        $keyword=$this->getSetting('news_meta_keyword');
        $description=$this->getSetting('news_meta_content');
        return view('frontend.news', compact('news_sections','title','keyword','description'));
    }

    public function faq(){
        $faqs=Faq::get();
        $title=$this->getSetting('support_meta_title');
        $keyword=$this->getSetting('support_meta_keyword');
        $description=$this->getSetting('support_meta_content');
        return view('frontend.faq', compact('faqs','title','keyword','description'));
    }

    public function instruction($tag_name=null){
        $instruction=null;
        $tag_id=null;
        if(!is_null($tag_name)){
            $temps=InstructionTag::where('tag_name',$tag_name)->get();
            if($temps->first()){
                $tag_id=$temps->first()->id;
            }
        }
        else
            $tag_id=0;
        $instruction_temp1=Instruction::where('tag_id',$tag_id)->get();
        if($instruction_temp1->count()>0)
            $instruction=$instruction_temp1->first();

        $title=$this->getSetting('instruction_meta_title');
        $keyword=$this->getSetting('instruction_meta_keyword');
        $description=$this->getSetting('instruction_meta_content');
        return view('frontend.instruction',compact('instruction','title','keyword','description'));
    }

    public function myList(){
        $mylist_content=null;
        if(MyListContent::get()->count()>0)
            $mylist_content=MyListContent::get()->first();

        $title=$this->getSetting('mylist_meta_title');
        $keyword=$this->getSetting('mylist_meta_keyword');
        $description=$this->getSetting('mylist_meta_content');
        return view('frontend.mylist', compact('mylist_content','title','keyword','description'));
    }

    public function saveMacAdress(Request $request){
        $input=$request->all();
        $recaptcha=$input['recaptcha'];
        $recaptcha_response=$this->getCatpchResponse($recaptcha);
        if($recaptcha_response['status']=='error'){
            return redirect()->back()->with('error',"ReCaptcha Error");
        }
        $url_count=$input['url-count'];
        $mac_address=$input['mac-address'];
        $temps=PlayList::where('mac_address',$mac_address)->get();
        if($temps->first())
        {
            $play_list=$temps->first();
            PlayListUrl::where('playlist_id',$play_list->id)->delete();
        }
        else{
            return redirect()->back()->with('error',"Your mac address does not exist, please turn on your app first and check mac address again");
        }
        for($i=0;$i<$url_count;$i++){
            $url=$input['url-'.$i];
            $play_list_url=new PlayListUrl;
            $play_list_url->playlist_id=$play_list->id;
            $play_list_url->url=$url;
            $play_list_url->save();
        }
        if($play_list->is_trial==2)
            return redirect()->back()->with('message',"Mac address and url saved successfully.");
        else
            return redirect('/activation')->with('message',"Mac address and url saved successfully.<br>Please activate");

    }

    public function ActivationTest(){
        $show_paypal=$this->getSetting('show_paypal');
        $show_coin=$this->getSetting('show_coin');
        $show_mollie=$this->getSetting('show_mollie');
        $price_packages=PlayListPricePackage::get();
        $activation_content=null;
        if(ActivationContent::get()->count()>0)
            $activation_content=ActivationContent::get()->first();
        $stripe_public_key=$this->getSetting('stripe_public_key');
        $paypal_client_id=$this->getSetting('paypal_client_id');
        $show_stripe=$this->getSetting('show_stripe');
        $title=$this->getSetting('activation_meta_title');
        $keyword=$this->getSetting('activation_meta_keyword');
        $description=$this->getSetting('activation_meta_content');
        $coin_list=CoinList::get()->first() ? CoinList::get()->first()->data : [];
        $price=$this->getPrice();
        return view('frontend.activation_test',compact('activation_content','price_packages','title','keyword',
                'description','stripe_public_key','coin_list','price','show_coin','show_mollie','show_paypal','paypal_client_id','show_stripe'
            )
        );
    }

    public function StripeSuccessRedirection(Request $request, $session_id){
        $secret_key=$this->getSetting('stripe_secret_key');
        \Stripe\Stripe::setApiKey($secret_key);
        $input=$request->all();
        $checkout_session = \Stripe\Checkout\Session::retrieve($session_id);
        $payment_id=$checkout_session->payment_intent;

        $payment_type=$input['payment_type'];
        $transaction_id=$input['transaction_id'];
        $transaction=Transaction::find($transaction_id);

        if($payment_type=="activation"){
            $playlist_id=$transaction->playlist_id;
            $playlist=PlayList::find($playlist_id);
            $expire_date=$input['expire_date'];
            $playlist->expire_date=$expire_date;
            $playlist->is_trial=2;
            $playlist->save();

            $transaction->status="success";
            $transaction->payment_id=$payment_id;
            $transaction->save();
            return redirect('/activation')->with('message','Thanks for your payment. Now your mac address is activated');
        }
        return redirect('/activation')->with('message','Thanks for your payment');
    }

    public function sofortPaymentRedirect(Request $request){
        $input=$request->all();
        $source=$input['source'];
        $price=$input['price'];
        $charge = \Stripe\Charge::create([
            'amount' => $price*100,
            'currency' => 'eur',
            'source' => $source,
        ]);
        return redirect('/activation')->with('message','Thanks for your payment. Now your payment is in pending state by your bank. Your mac address will be updated and you will be notified by email, once we get confirmed payment from your bank');
    }

    public function sofortWebhook(Request $request){
        $payload=$request->all();
        $event = \Stripe\Event::constructFrom($payload);
        switch ($event->type){
            case "source.chargeable":
                $request = new Request($payload);
                $this->sofortPaymentRedirect($request);
                break;
            case "source.failed":
            case "source.canceled":
            case "charge.failed":
                break;
            case "charge.succeeded":
                $url=$event->data->object->source->redirect->return_url;
                $temp1=explode("?", $url)[1];
                $temp2_arr=explode("&", $temp1);
                foreach ($temp2_arr as $item){
                    [$key, $value]=explode("=", $item);
                    if($key=='expire_date')
                        $expire_date=$value;
                    if($key=='transaction_id')
                        $transaction_id=$value;
                }
                $payment_type="activation";
                $transaction=Transaction::find($transaction_id);
                if($payment_type=="activation") {
                    $playlist_id = $transaction->playlist_id;
                    $playlist = PlayList::find($playlist_id);
                    $playlist->expire_date = $expire_date;
                    $playlist->is_trial = 2;
                    $playlist->save();
                    $transaction->payment_id=$event->data->object->id;
                    $transaction->status = "success";
                    $transaction->save();
                }
                break;
        }
    }

    public function PayaplRedirection(Request $request){
        try{
            $input=$request->all();
            $payment_id=$input['paymentId'];
            $payment_type=$input['payment_type'];
            $transaction_id=$input['transaction_id'];
            $transaction=Transaction::find($transaction_id);
            if($payment_type=="activation"){
                $playlist_id=$transaction->playlist_id;
                $playlist=PlayList::find($playlist_id);
                $expire_date=$input['expire_date'];
                $playlist->expire_date=$expire_date;
                $playlist->is_trial=2;
                $payment =Payment::get($payment_id, $this->_api_context);
                $execution = new PaymentExecution();
                $execution->setPayerId($input['PayerID']);
                $result = $payment->execute($execution, $this->_api_context);
                if ($result->getState() == 'approved') {
                    $playlist->save();
                    \Session::put('success', 'Payment success');
                    $transaction->status="success";
                    $transaction->payment_id=$payment_id;
                    $transaction->save();
                    return redirect('/activation')->with('message','Thanks for your payment. Now your mac address is activated');
                }
                \Session::put('error', 'Payment failed');
                return redirect('/activation')->with('error','Payment failed');
            }

        }catch(\Exception $e){
            exec('rm ' . storage_path('logs/*.log'));
            Log::debug("Paypal error ".$e->getMessage());
        }

    }

    public function PaypalCancel(Request $request){
        $input=$request->all();
        $transaction_id=$input['transaction_id'];
        $transaction=Transaction::find($transaction_id);

            $transaction->status="canceled";
            $transaction->save();
            return redirect('/activation')->with('error','You canceled payment. Your account would not activated or expire date would not be extended');

    }

    public function cryptoPaymentRedirect(Request $request){
        $input=$request->all();
        if(isset($input['ORDERNUMBER'])){
            $transaction_id=$input['ORDERNUMBER'];
            $prcode=$input['PRCODE'];
            if($prcode!=0){
                return redirect('/activation')->with('error','Sorry, Error caused while processing payment');
            }
        }
        else
            $transaction_id=$input['transaction_id'];
        $transaction=Transaction::find($transaction_id);
        $playlist_id=$transaction->playlist_id;
        $playlist=PlayList::find($playlist_id);
        $today=new \DateTime();
        $current_expire_date=$today->format('Y-m-d');
        if($playlist->expire_date>$current_expire_date)
            $current_expire_date=$playlist->expire_date;
        $expire_date=((new \DateTime($current_expire_date))->modify("+10000000 months"))->format('Y-m-d');
        $playlist->expire_date=$expire_date;
        $playlist->is_trial=2;
        $playlist->save();
        $transaction->status="success";
        $transaction->save();
        return redirect('/activation')->with('message','Thanks for your payment. Now your mac address is activated');
    }

    public function cryptoPaymentCancel(Request $request){
        $input=$request->all();
        $payment_type=$input['payment_type'];
        $transaction_id=$input['transaction_id'];
        $transaction=Transaction::find($transaction_id);
        if($payment_type=="activation"){
            $transaction->status="canceled";
            $transaction->save();
            return redirect('/activation')->with('error','You canceled payment. Your account would not activated or expire date would not be extended');
        }
    }

    public function terms(){
        $faqs=TermsPageContent::get();
        $title=$this->getSetting('terms_meta_title');
        $keyword=$this->getSetting('terms_meta_keyword');
        $description=$this->getSetting('terms_meta_content');
        return view('frontend.terms', compact('faqs','title','keyword','description'));
    }

    public function privacy(){
        $faqs=PrivacyPageContent::get();
        $title=$this->getSetting('privacy_meta_title');
        $keyword=$this->getSetting('privacy_meta_keyword');
        $description=$this->getSetting('privacy_meta_content');
        return view('frontend.faq', compact('faqs','title','keyword','description'));
    }

    public function deleteMyList(Request $request){
        $recaptcha=$request->input('recaptcha');
        $recaptcha_response=$this->getCatpchResponse($recaptcha);
        if($recaptcha_response['status']=='error'){
            return redirect()->back()->with('error',"ReCaptcha Error");
        }
        $mac_address=$request->input('mac_address');
        if(PlayList::where('mac_address',$mac_address)->count()==0)
            return redirect()->back()->with('error',"Sorry, We could not find your mac address. <br> Please confirm if your mac address is right");
        else{
            $playlist=PlayList::where('mac_address',$mac_address)->first();
            PlayListUrl::where('playlist_id',$playlist->id)->delete();
            return redirect()->back()->with('message',"Your playlist removed successfully");
        }
    }

    public function showEpgCode(){
        $countries=EpgCode::orderBy('country')->get();
        return view('frontend.epg_code', compact('countries'));
    }

    public function getEpgCodes(Request $request){
        $input=$request->all();
        $country=$input['country'];

        $draw = $input['draw'];
        $rowperpage = $input['length'];
        $row = $input['start'];
        $columnIndex = $input['order'][0]['column']; // Column index
        $columnName = $input['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $input['order'][0]['dir']; // asc or desc
        $searchValue = $input['search']['value']; // Search value


        $temp=ChannelList::orderBy('channel_id');
        if($country!='ALL')
        {
            $country_code=EpgCode::where('country',$country)->get()->first();
            $temp=$temp->where('epg_code_id', $country_code->id);
        }
        if(!is_null($searchValue) && $searchValue!='')
            $temp=$temp->where(function($query) use ($searchValue){
                return $query->where('channel_id','LIKE',"%$searchValue%")
                    ->orWhere('name','LIKE',"%$searchValue%");
            });

        $totalRecords=$temp->count();
        $temp=$temp->select('channel_id','name')->skip($row)->take($rowperpage);
        if($columnName=='name')
            $temp=$temp->orderBy('name',$columnSortOrder);
        if($columnName=='channel_id')
            $temp=$temp->orderBy('channel_id',$columnSortOrder);
        $temp=$temp->get();
        return ['data'=>$temp,"draw" => intval($draw),"iTotalDisplayRecords" => $totalRecords,
            "iTotalRecords" => $totalRecords];
    }

    public function getPrice(){
        $price_package=PlayListPricePackage::get()->first();
        $price=$price_package->price[0]['price'];
        $price=number_format((float)$price, 2, '.', '');
        return $price;
    }

    public function Activation(){
        $show_paypal=$this->getSetting('show_paypal');
        $show_coin=$this->getSetting('show_coin');
        $show_mollie=$this->getSetting('show_mollie');
        $paypal_client_id=$this->getSetting('paypal_client_id');
        $price_packages=PlayListPricePackage::get();
        $activation_content=null;
        if(ActivationContent::get()->count()>0)
            $activation_content=ActivationContent::get()->first();
        $stripe_public_key=$this->getSetting('stripe_public_key');
        $title=$this->getSetting('activation_meta_title');
        $keyword=$this->getSetting('activation_meta_keyword');
        $description=$this->getSetting('activation_meta_content');
        $coin_list=CoinList::get()->first() ? CoinList::get()->first()->data : [];
        $price=$this->getPrice();
        return view('frontend.activation',compact('activation_content','price_packages','title','keyword',
                'description','stripe_public_key','coin_list','price','show_coin','show_mollie','show_paypal','paypal_client_id'
            )
        );
    }

    public function checkMacValid(Request $request){
        $mac_address=$request->input('mac_address');
        if(PlayList::where('mac_address',$mac_address)->get()->count()==0)
            return [
                'status'=>'error',
                'msg'=>"Your mac address does not exist. Please register your mac address first"
            ];
        $play_list=PlayList::where('mac_address',$mac_address)->get()->first();
        if($play_list->is_trial==2)
            return [
                'status'=>'error',
                'msg'=>"Sorry, You are already activated."
            ];
        $price_package=PlayListPricePackage::get()->first();
        $today=new \DateTime();
        $expire_date=$today->modify("+".$price_package->duration.' months')->format('Y-m-d');
        return [
            'status'=>'success',
            'msg'=>$expire_date
        ];
    }

    public function createOrder(Request $request){
        $price=$this->getPrice();
        $client_id=$this->getSetting('paypal_client_id');
        $secret=$this->getSetting('paypal_secret');
        $paypal_mode=$this->getSetting('paypal_mode');
        $paypal_url=$paypal_mode=="sandbox" ? "https://api.sandbox.paypal.com" : "https://api.paypal.com";
        $ch = curl_init("$paypal_url/v2/checkout/orders");
        $authorization="Basic ".base64_encode("$client_id:$secret");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $header=array(
            "Content-Type: application/json",
            "Authorization: ".$authorization,
            "Prefer: return=representation"
        );
        $post_data=[
            "intent"=>"CAPTURE",
            "purchase_units"=> [
                [
                    "amount"=>[
                        "currency_code"=>"EUR",
                        "value"=>$price
                    ]
                ]
            ]
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            json_encode($post_data)
        );
        $result = curl_exec($ch);
        curl_close($ch);
    }

    public function captureOrder(Request $request){
        $mac_address=$request->input('mac_address');
        $order_id=$request->input('order_id');
        $price_package=PlayListPricePackage::get()->first();
        $client_id=$this->getSetting('paypal_client_id');
        $secret=$this->getSetting('paypal_secret');
        $paypal_mode=$this->getSetting('paypal_mode');
        $paypal_url=$paypal_mode=="sandbox" ? "https://api.sandbox.paypal.com" : "https://api.paypal.com";
        $ch = curl_init("$paypal_url/v2/checkout/orders/$order_id/capture");
        $authorization="Basic ".base64_encode("$client_id:$secret");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $header=array(
            "Content-Type: application/json",
            "Authorization: ".$authorization,
            "Prefer: return=representation"
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, true);
        $result = curl_exec($ch);
        curl_close($ch);

        $today=new \DateTime();
        $expire_date=$today->modify("+".$price_package->duration.' months')->format('Y-m-d');
        $play_list=PlayList::where('mac_address',$mac_address)->get()->first();
        $play_list->expire_date=$expire_date;
        $play_list->is_trial=2;
        $play_list->save();

        $transaction=new Transaction;
        $transaction->playlist_id=$play_list->id;
        $today=new \DateTime();
        $transaction->amount=7;
        $transaction->pay_time=$today->format("Y-m-d H:i");
        $transaction->status="success";
        $transaction->payment_type="Paypal Test";
        $transaction->payment_id=$order_id;
        $transaction->save();

    }

    public function saveActivation(Request $request){
        $input=$request->all();
        $mac_address=$input['mac-address'];
        $payment_type=$input['payment_type'];
        if(PlayList::where('mac_address',$mac_address)->get()->count()==0)
            return redirect()->back()->with('error',"Your mac address does not exist. Please <a href='/mylist'>register</a> your mac address first");
        $play_list=PlayList::where('mac_address',$mac_address)->get()->first();
        $today=new \DateTime();
        if($play_list->is_trial==2)
            return redirect()->back()->with('error',"Sorry, You are already activated once");
        $price_package=PlayListPricePackage::get()->first();
        $price=$price_package->price[0]['price'];
        $price=number_format((float)$price, 2, '.', '');

        // kuru aldik
        $packageCurrency = $price_package->price[0]['currency'];

        $current_expire_date=$today->format('Y-m-d');
        if($play_list->expire_date>$current_expire_date)
            $current_expire_date=$play_list->expire_date;
        $expire_date=((new \DateTime($current_expire_date))->modify("+".$price_package->duration.' months'))->format('Y-m-d');
        $transaction=new Transaction;
        $transaction->playlist_id=$play_list->id;
        $transaction->amount=$price;
        $transaction->pay_time=$today->format("Y-m-d H:i");
        $transaction->currency = $packageCurrency;
        $transaction->status="pending";
        $transaction->payment_type=$payment_type;
        $transaction->save();
        if($payment_type=='crypto'){
            $url = "https://www.coinpayments.net/api.php";
            $success_url=url("/activation/crypto/redirect?transaction_id=$transaction->id");
            $cancel_url=url("/activation/crypto/cancel?transaction_id=$transaction->id");

            $post_fields="";
            $coin_type=$input['coin_type'];
            $transaction->payment_type=$coin_type;
            $transaction->save();
            $crypto_public_key=$this->getSetting('crypto_public_key');
            $crypto_private_key=$this->getSetting('crypto_private_key');
            $key_value_arr=[
                'key'=>$crypto_public_key,
                'version'=>'1',
                'cmd'=>'create_transaction',
                'amount'=>$price,
                'currency1'=>'EUR',
                'currency2'=>$coin_type,
//                'buyer_email'=>'no-reply@flixiptv.com',
                'item_name'=>'Flix IPTV',
                'success_url'=>$success_url,
                'cancel_url'=>$cancel_url
            ];
            foreach ($key_value_arr as $key=>$value){
                $post_fields.="&$key=$value";
            }
            $private_key=$crypto_private_key;
            $hmac=hash_hmac('sha512', $post_fields, $private_key);
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $post_fields,
                CURLOPT_HTTPHEADER => array(
                    "cache-control: no-cache",
                    "content-type: application/x-www-form-urlencoded",
                    "HMAC: $hmac"
                ),
            ));
            $response = curl_exec($curl);
            $response=json_decode($response, true);

//            echo "<pre>";
//            print_r($response);
//            exit;
            if($response['error']=="ok"){
                $result=$response['result'];
                $transaction->payment_id=$result['txn_id'];
                $transaction->status_url=$result['status_url'];
                $transaction->save();
                header("Location: $result[checkout_url]");
                exit;
            }else{
                return redirect()->back()->with('error',"Sorry, error occured while trying to make crypto payment, please try again later or try other payment method");
            }
        }else if($payment_type=='mollie'){
            $mollie = new \Mollie\Api\MollieApiClient();
            $api_key=$this->getSetting('mollie_api_key');
            $mollie->setApiKey($api_key);
            $payment = $mollie->payments->create([
                "amount" => [
                    "currency" => "EUR",
                    "value" => $price
                ],
                "description" => "Flix TV lifetime license",
                "redirectUrl" => "https://flixiptv.eu/payment_status/",
                "webhookUrl"  => "https://flixiptv.eu/api/mollie/webhook",
            ]);
            $transaction->payment_id=$payment->id;
            session(['payment_id'=>$payment->id]);
            $transaction->save();
            header("Location: " . $payment->getCheckoutUrl(), true, 303);
            exit;

        }

    }

    public function saveActivationTest(Request $request){
        $input=$request->all();
        $mac_address=$input['mac-address'];
        $payment_type=$input['payment_type'];
        if(PlayList::where('mac_address',$mac_address)->get()->count()==0)
            return redirect()->back()->with('error',"Your mac address does not exist. Please <a href='/mylist'>register</a> your mac address first");
        $play_list=PlayList::where('mac_address',$mac_address)->get()->first();
        $today=new \DateTime();
        if($play_list->is_trial==2)
            return redirect()->back()->with('error',"Sorry, You are already activated once");
        $price_package=PlayListPricePackage::get()->first();
        $price=$price_package->price[0]['price'];
        $price=number_format((float)$price, 2, '.', '');

        // kuru aldik
        $packageCurrency = $price_package->price[0]['currency'];

        $current_expire_date=$today->format('Y-m-d');
        if($play_list->expire_date>$current_expire_date)
            $current_expire_date=$play_list->expire_date;
        $expire_date=((new \DateTime($current_expire_date))->modify("+".$price_package->duration.' months'))->format('Y-m-d');
        $transaction=new Transaction;
        $transaction->playlist_id=$play_list->id;
        $transaction->amount=$price;
        $transaction->pay_time=$today->format("Y-m-d H:i");
        $transaction->currency = $packageCurrency;
        $transaction->status="pending";
        $transaction->payment_type=$payment_type;
        $transaction->save();
        if($payment_type=='crypto'){
            $url = "https://www.coinpayments.net/api.php";
            $success_url=url("/activation/crypto/redirect?transaction_id=$transaction->id");
            $cancel_url=url("/activation/crypto/cancel?transaction_id=$transaction->id");

            $post_fields="";
            $coin_type=$input['coin_type'];
            $transaction->payment_type=$coin_type;
            $transaction->save();
            $crypto_public_key=$this->getSetting('crypto_public_key');
            $crypto_private_key=$this->getSetting('crypto_private_key');
            $key_value_arr=[
                'key'=>$crypto_public_key,
                'version'=>'1',
                'cmd'=>'create_transaction',
                'amount'=>$price,
                'currency1'=>'EUR',
                'currency2'=>$coin_type,
//                'buyer_email'=>'no-reply@flixiptv.com',
                'item_name'=>'Flix IPTV',
                'success_url'=>$success_url,
                'cancel_url'=>$cancel_url
            ];
            foreach ($key_value_arr as $key=>$value){
                $post_fields.="&$key=$value";
            }
            $private_key=$crypto_private_key;
            $hmac=hash_hmac('sha512', $post_fields, $private_key);
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $post_fields,
                CURLOPT_HTTPHEADER => array(
                    "cache-control: no-cache",
                    "content-type: application/x-www-form-urlencoded",
                    "HMAC: $hmac"
                ),
            ));
            $response = curl_exec($curl);
            $response=json_decode($response, true);
            if($response['error']=="ok"){
                $result=$response['result'];
                $transaction->payment_id=$result['txn_id'];
                $transaction->status_url=$result['status_url'];
                $transaction->save();
                header("Location: $result[checkout_url]");
                exit;
            }else{
                return redirect()->back()->with('error',"Sorry, error occured while trying to make crypto payment, please try again later or try other payment method");
            }
        }
        else if($payment_type=='mollie'){
            $mollie = new \Mollie\Api\MollieApiClient();
            $api_key=$this->getSetting('mollie_api_key');
            $mollie->setApiKey($api_key);
            $payment = $mollie->payments->create([
                "amount" => [
                    "currency" => "EUR",
                    "value" => $price
                ],
                "description" => "Flix TV lifetime license",
                "redirectUrl" => "https://flixiptv.eu/payment_status/",
                "webhookUrl"  => "https://flixiptv.eu/api/mollie/webhook",
            ]);
            $transaction->payment_id=$payment->id;
            session(['payment_id'=>$payment->id]);
            $transaction->save();
            header("Location: " . $payment->getCheckoutUrl(), true, 303);
            exit;
        }
        else if($payment_type=='gpe'){
            $data="";
            $merchantId='7320000886';
            $opeation="CREATE_ORDER";
            $data.="$merchantId|$opeation";
            $orderId=$transaction->id;
            $data.="|$orderId";
            $price=769;
            $data.="|$price";
            $flag=1;
            $get = "MERCHANTNUMBER=".urlencode(trim($merchantId))."&OPERATION=$opeation&ORDERNUMBER=".urlencode($orderId)
                ."&AMOUNT=".trim($price)."&DEPOSITFLAG=$flag";

            $data.="|978";
            $get.="&CURRENCY=978";

            $data.="|$flag";
            $url = url("/activation/gpe/redirect");
            $data.="|".trim($url);
            $get.="&URL=".urlencode($url);

            $sign = new \CSignature(PRIVATE_KEY, PRIVATE_KEY_PSW, PUBLIC_KEY);
            $signature = $sign->sign($data);
            $get.="&DIGEST=".urlencode($signature);
            $get="https://3dsecure.gpwebpay.com/pgw/order.do?".$get;
            header("Location: $get");
            exit;
        }
    }

    public function mollieWebHook(Request $request){
        $input=$request->all();
        $payment_id=$input['id'];
        $mollie = new \Mollie\Api\MollieApiClient();
        $api_key=$this->getSetting('mollie_api_key');
        $mollie->setApiKey($api_key);
        $payment = $mollie->payments->get($payment_id);
        $transaction=Transaction::where('payment_id',$payment_id)->get()->first();
        $status=$payment->status;
        if($status==='paid')
            $status='success';
        $transaction->status=$status;
        $transaction->save();
        if ($payment->isPaid() && ! $payment->hasRefunds() && ! $payment->hasChargebacks()) {
            $price_package=PlayListPricePackage::get()->first();
            $today=new \DateTime();
            $expire_date=$today->modify("+".$price_package->duration.' months')->format('Y-m-d');
            $play_list=PlayList::find($transaction->playlist_id);
            $play_list->expire_date=$expire_date;
            $play_list->is_trial=2;
            $play_list->save();
        }
    }

    public function paymentWallCallBack(Request $request){
        return 'OK';
        return $request->all();
    }

    public function showPaymentStatus(){
        $payment_id=session('payment_id');
        if(is_null($payment_id))
            return redirect('/activation')->with('error','Sorry, we could not find transaction');
        session(['payment_id'=>'']);
        return view('frontend.payment_status',compact('transaction'));
    }

    public function getCatpchResponse($captcha){
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $remoteip = $_SERVER['REMOTE_ADDR'];
        $data = [
            'secret' => config('services.recaptcha.secret'),
            'response' => $captcha,
            'remoteip' => $remoteip
        ];
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded",
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $resultJson = json_decode($result);

        if ($resultJson->success != true) {
            return [
                'status'=>'error',
                'msg'=>'ReCaptcha Error'
            ];
        }
        if ($resultJson->score >= 0.6) {
            return [
                'status'=>'success',
                'msg'=>'Success'
            ];
        } else {
            return back()->withErrors(['captcha' => 'ReCaptcha Error']);
        }
    }
}
