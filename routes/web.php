<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\RoleController;
use Goutte\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']], function () {
    Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
        Route::get('/dashboard',[AdminDashboardController::class,'index'])->name('dashboard');
        Route::resource('products',ProductController::class);
    });


    Route::get('test/amazon',function (){
        $client = new Client();
        $url = 'https://www.amazon.com/All-Metal-Dacoity-Multimedia-Anti-ghosting-Waterproof/dp/B09TZWLFLY/ref=sr_1_3?keywords=gaming+keyboard&pd_rd_r=c41fa7d3-3f9a-4535-858f-213b6da5f3df&pd_rd_w=JDUP7&pd_rd_wg=lweT8&pf_rd_p=12129333-2117-4490-9c17-6d31baf0582a&pf_rd_r=VC1GJXPWMPXV8VSR8ZBJ&qid=1681615286&sr=8-3&language=ar_AE&currency=EGP';
        $client->setServerParameter('HTTP_USER_AGENT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3');
        $crawler = $client->request('GET', $url);
        $price = $crawler->filter('.a-price-whole')->text();
        return response()->json(['price' => $price, 'time' => now()]);
    });
    Route::get('test/noon',function (){
        $client = new Client();
        $url = 'https://www.noon.com/egypt-en/maybelline-lifter-lip-gloss-008-stone/N43092737A/p/?o=d802a5e2b636519a';
        $client->setServerParameter('HTTP_USER_AGENT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3');
        $crawler = $client->request('GET', $url);
    //    print_r($crawler->filter('.priceNow')->text());
        $price = $crawler->filter('.priceNow')->text();
        return response()->json(['price' => $price, 'time' => now()]);

    });
    Route::get('test/view',function (){
       return view('scraper') ;
    });


});


Route::get('test/socket-send',function (){
    event(new App\Events\AmazonPriceNotify('Hello World'));
});



Route::get('test/socket-receive',function (){
    return view('socket');
});


Route::resource('roles',RoleController::class);


Route::get('/save-token', function (Request $request) {
    auth()->user()->update(['fcm_token'=>$request->token]);
    return response()->json(['token saved successfully.']);
})->name('save-push-notification-token');

Route::get('/message', function () {


    $SERVER_API_KEY = 'AAAAgzxdbFg:APA91bHpS8bkZRzv9EfT4U1QVmkIoCnUDKVJP5fDYEcsIkz2hoB73og1ooWcGj2JUpdN2KRRttcBUfrt67Im6CqrwHd7sV-fo4hW6MV7kPgSrg_lFqJQcNnJVSlyIoCzPY0IzxOB0RbQ';
    $SENDER_ID = '563653471320';
    $token_1 = 'fWqoHDx6TsOoE5w5-Ze9Hn:APA91bHnByu5LIUp_aZ9JNjyU7aXDdH33LtAPRwW-5Ea-Hz-3IO7oUGF9-PtOzL6JBs_K5Lw1M2hu-AIgkc_dBv6hW5uv-cvuxUZDWkkKjdj10hVLZJ6boDuNoKk6uv2h7baPXY55yjz';

    $data = [

        "registration_ids" => [
            $token_1
        ],
        "notification" => [

            "title" => 'test message for abo adel ',

            "body" => 'عايزين نفطر يسطا ',

            "sound"=> "default" // required for sound on ios

        ],

        "data" => [
            "product_id" => "1"
        ]
    ];

    $dataString = json_encode($data);

    $headers = [
        'Authorization: key=' . $SERVER_API_KEY,
        'SenderId:' . $SENDER_ID,
        'Content-Type: application/json',
    ];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');

    curl_setopt($ch, CURLOPT_POST, true);

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

    $response = curl_exec($ch);

//    dd($response);

    return $response;

});
