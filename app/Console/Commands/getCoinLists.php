<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class getCoinLists extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:coins';

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
     * @return mixed
     */
    public function handle()
    {
        $url = "https://www.coinpayments.net/api.php";
        $post_fields="";
        $key_value_arr=[
            'key'=>"bd99bfed1e98dea341bdda5674409319f82575d67459eb2ae88f8511543e9d06",
            'version'=>'1',
            'cmd'=>'rates',
            'accepted'=>'2',
        ];
        foreach ($key_value_arr as $key=>$value){
            $post_fields.="&$key=$value";
        }
        $private_key="4aa591bdd9f16D27Cd54c3a1979bbD462B782E8F56d3BA58798823b983d9aB6A";
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
            print_r($result);
            Log::debug(json_encode($result));
            exit;
        }else{
            return redirect()->back()->with('error',"Sorry, error occured while trying to make crypto payment, please try again later or try other payment method");
        }
    }
}
