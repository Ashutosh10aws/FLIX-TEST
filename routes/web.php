<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('frontend.index');
});

Route::get('/phpinfo',function (){
    phpinfo();
});

Auth::routes();

Route::get('/',function (){
   return redirect('/mylist');
});
Route::get('/test',function (){
    return (dirname(__FILE__));

});


Route::get('/home', 'HomeController@index')->name('home');
Route::get('/news','HomeController@news');
Route::get('/faq','HomeController@faq');
Route::get('/instruction/{tag_name?}','HomeController@instruction');
Route::get('/mylist','HomeController@myList');
Route::post('/mylist/saveMacAdress','HomeController@saveMacAdress');
Route::post('/mylist/delete','HomeController@deleteMyList');

Route::get('/activation/stripe/success/{session_id}','HomeController@StripeSuccessRedirection');
Route::get('/activation/paypal/redirection','HomeController@PayaplRedirection');
Route::any('/activation/paypal/cancel','HomeController@PaypalCancel');

Route::any('/activation/sofort/redirect','HomeController@sofortPaymentRedirect');
Route::any('/activation/sofort/webhook','HomeController@sofortWebhook');

Route::any('/activation/crypto/redirect','HomeController@cryptoPaymentRedirect');

Route::any('/activation/gpe/redirect','HomeController@cryptoPaymentRedirect');

Route::any('/activation/crypto/cancel','HomeController@cryptoPaymentCancel');


Route::post('/checkMacValid','HomeController@checkMacValid');

Route::get('/terms&conditions','HomeController@terms');
Route::get('/privacy&policy','HomeController@privacy');
Route::get('/sitemap.xml',function (){
    $xmlString=file_get_contents(public_path("sitemap.xml"));
    return response()->view('frontend.sitemap',compact('xmlString') )->header('Content-Type', 'text/xml');
});
Route::get('/epg-codes','HomeController@showEpgCode');
Route::post('/getEpgCodes','HomeController@getEpgCodes');

Route::get('/activation','HomeController@Activation');
Route::post('/activation/saveActivation','HomeController@saveActivation');

Route::get('/activation-test','HomeController@ActivationTest');
Route::post('/activation/saveActivationTest','HomeController@saveActivationTest');


Route::post('/paypal/order/create','HomeController@createOrder');
Route::post('/paypal/order/capture','HomeController@captureOrder');

Route::post('/check-recaptcha','HomeController@checkReCaptCha');

//Route::get('/mollie/webhook','HomeController@mollieWebHook');
Route::get('/payment_wall_callback','HomeController@paymentWallCallBack');
Route::get('/payment_status','HomeController@showPaymentStatus');

//Route::post('/payizone/callback','HomeController@payizoneCallBack')->name('payizoneCallBack');



Route::Group(['prefix'=>'admin','namespace'=>'Admin'], function (){
    Route::get('/login','Auth\LoginController@showLoginForm');
    Route::post('/login','Auth\LoginController@login')->name('admin.login');
    Route::get('/register','Auth\RegisterController@showRegistrationForm');
    Route::post('/register','Auth\RegisterController@register',function (){
        return "Sorry, stop your scam register";
    });


    Route::Group(['middleware'=>'auth:admin'],function (){
        Route::post('/logout','Auth\LoginController@logOut')->name('admin.logout');
        Route::get('/logout','Auth\LoginController@logOut');
        Route::Group(['middleware'=>'is_admin'],function (){
            Route::get('/news','NewsController@index');
            Route::get('/news/create/{id?}','NewsController@createNewsSection');
            Route::post('/news/save','NewsController@saveNewsSection');
            Route::post('/news/delete/{id}','NewsController@deleteNewsSection');

            Route::get('/faq','FaqController@index');
            Route::post('/faq/save','FaqController@save');

            Route::get('instruction/tags','InstructionController@Tags');
            Route::post('instruction/createTag','InstructionController@createTag');
            Route::post('instruction/deleteTag/{id}','InstructionController@deleteTag');
            Route::get('instruction/page/{tag_id}','InstructionController@instructionTag');
            Route::post('instruction/page/save/{tag_id}','InstructionController@saveInstructionPage');

            Route::get('mylist','AdminController@showMyListPageContent');
            Route::post('mylist/save','AdminController@saveMyListContent');

            Route::get('activation','AdminController@showActivationPageContent');
            Route::post('activation/save','AdminController@saveActivationContent');

            Route::get('playlist_package','PlayListPriceController@index');
            Route::get('playlist_package/create/{id?}','PlayListPriceController@createPackage');
            Route::post('playlist_package/delete/{id}','PlayListPriceController@deletePackage');
            Route::post('playlist_package/save','PlayListPriceController@savePackage');

            Route::get('terms','AdminController@showTermsContent');
            Route::post('terms/save','AdminController@saveTermsContent');

            Route::get('privacy','AdminController@showPrivacyContent');
            Route::post('privacy/save','AdminController@savePrivacyContent');

            Route::get('playlist/{id}','PlayListController@showDetail');

            Route::get('transactions','AdminController@transactions');
            Route::post('getTransactions','AdminController@getTransactions');


            Route::get('seo_setting','AdminController@showSeoSetting');
            Route::post('saveSeoSetting','AdminController@saveSeoSetting');

            Route::get('showDemoUrl','AdminController@showDemoUrl');
            Route::post('saveDemoUrl','AdminController@saveDemoUrl');

            Route::get('showAppBackground','AdminController@showAppBackground');
            Route::post('saveThemes','AdminController@saveThemes');

            Route::get('showAdverts','AdminController@showAdverts');
            Route::post('saveAdverts','AdminController@saveAdverts');

            Route::get('showStripeSetting','AdminController@showStripeSetting');
            Route::post('saveStripeSetting','AdminController@saveStripeSetting');

            Route::get('showPaypalSetting','AdminController@showPaypalSetting');
            Route::post('savePaypalSetting','AdminController@savePaypalSetting');

            Route::get('showCryptoApiKey','AdminController@showCryptoApiKey');
            Route::post('saveCryptoApiKey','AdminController@saveCryptoApiKey');

            Route::get('showCoinList','AdminController@showCoinList');
            Route::post('saveCoinList','AdminController@saveCoinList');

            Route::get('epg-code','EpgController@showEpgCode');
            Route::post('epg/create','EpgController@create');
            Route::post('epg/delete/{id}','EpgController@delete');

            Route::get('epg-server','EpgController@showEpgServer');
            Route::post('epg-server/create','EpgController@createServer');
            Route::post('epg-server/delete/{id}','EpgController@deleteServer');

            Route::get('android-update','AdminController@showAndroidUpdate');
            Route::post('saveAndroidUpdate','AdminController@saveAndroidUpdate');

            Route::get('language','AdminController@showLanguage');
            Route::post('language/create','AdminController@createLanguage');
            Route::post('language/delete/{id}','AdminController@deleteLanguage');

            Route::get('word','AdminController@showWord');
            Route::post('word/create','AdminController@createWord');
            Route::post('word/delete/{id}','AdminController@deleteWord');

            Route::get('language-word/{language_id}','AdminController@showLanguageWord');
            Route::post('saveLanguageWord/{language_id}','AdminController@saveLanguageWord');
            Route::post('saveLanguageFile/{language_id}','AdminController@saveLanguageFile');

            Route::get('showMollieSetting','AdminController@showMollieSetting');
            Route::post('saveMollieSetting','AdminController@saveMollieSetting');

            Route::get('showPaymentVisibility','AdminController@showPaymentVisibility');
            Route::post('savePaymentVisibility','AdminController@savePaymentVisibility');

            Route::get('resellers','AdminController@showResellers');
            Route::post('reseller/create','AdminController@createReseller');
            Route::post('reseller/delete','AdminController@deleteReseller');

            Route::get('remove-old-device','AdminController@showRemoveOldDevice');
            Route::post('postRemoveDevice','AdminController@postRemoveDevice');
        });
    });
    Route::get('playlists','PlayListController@index');
    Route::post('playlist/getPlaylists','PlayListController@getPlaylists');
    Route::post('playlist/activate','PlayListController@activate');
    Route::get('/','AdminController@index');

    Route::get('profile','AdminController@showProfile');
    Route::post('updateProfile','AdminController@updateProfile');
});
