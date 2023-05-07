<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Jobs\ScrapeProduct;
use App\Models\Product;
use App\Models\Proxy;
use App\Models\User;
use Goutte\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Sunra\PhpSimple\HtmlDomParser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpClient\HttpClient;


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
        Route::resource('users',UserController::class);
        Route::resource('products',ProductController::class);
    });




});




Route::resource('roles',RoleController::class);


Route::get('/save-token', function (Request $request) {
    auth()->user()->update(['fcm_token'=>$request->token]);
    return response()->json(['token saved successfully.']);
})->name('save-push-notification-token');

Route::get('/message', function () {


    $SERVER_API_KEY = 'AAAAIe0Crhg:APA91bH5Vf1j3Gaay-z4hNn-GKxzbbwk_QCA3khn_2ic7GUkB-7fecJjiiXaqrgQJ6XItOdGEqlsoQWYuOSJGcheJrq_3OlK4UyIROoI9JQrcyqoAQdcAMvjZJ_IP9b-MqO9BoWwY7ML';
    $SENDER_ID = '145710296600';
    $token_1 = 'cNy1ELziRc6dGmONWJwMkk:APA91bEXh3XHtKyU2pdPDG2DA7RUA2oSspEftN7thJKZyZkiVb0uW-74bxHBIbmDASOvGn6Mb8gZ-jPcVzviZmExrpUE8XT6UoZI51cNbl5jpJg6pVmvxpQD_CphS99fe6GJ98va1-NC';

    $data = [
        "registration_ids" => [
            $token_1
        ],
        "notification" => [
            "title" => "New message",
            "body" => "You have a new message",
            'android_channel_id' => 'x-tracker-id',
            'sound' => 'notification',
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

    return $response;

}   );

Route::get('/proxy',function (){

    $product = \App\Models\Product::find(1);
    try {
        $proxy = DB::table('proxies')
            ->inRandomOrder()
            ->first();

        if (!$proxy) {
            // If no proxy found, scrape without proxy
            $client = HttpClient::create();
        } else {
            // Create an HTTP client with the proxy
            $client = HttpClient::create([
                'proxy' => sprintf('http://%s:%d', $proxy->ip, $proxy->port),
                ]);
        }

        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3';
        $response = $client->request('GET', $product->url, [
            'headers' => [
                'User-Agent' => $userAgent,
            ],
        ]);

        $statusCode = $response->getStatusCode();

        if ($statusCode != 200) {
            // If the status code is not 200, mark the proxy as failed
            if ($proxy) {
                DB::table('proxies')->where('id', $proxy->id)->update(['status' => 0]);
            }
            Log::info('proxy status false');
        }

        $html = $response->getContent();

        return $html;

        if ($product->platform == 'amazon') {
            return $this->extractAmazon($html);
        } elseif ($product->platform == 'noon') {
            return $this->extractNoon($html);
        }
    } catch (TransportException $exception) {
        if ($proxy) {
            DB::table('proxies')->where('id', $proxy->id)->update(['status' => 0]);
        }
        return $exception->getMessage();
    }catch(Exception $ex){
        return "general " . $ex->getMessage();
    }
});

Route::get('scrap-proxy',function (){
    $client = new Client();

    $url = 'https://free-proxy-list.net/';

    $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3';
    $crawler =  $client->request('GET', $url, [
        'headers' => [
            'User-Agent' => $userAgent,
        ],
    ]);

    $rows = array();
    $table = $crawler->filter('.table-striped tbody tr')->each(function ($row) {
        return  array(
            'ip' => $row->filter('td:nth-child(1)')->text(),
            'port' => $row->filter('td:nth-child(2)')->text(),
            'code' => $row->filter('td:nth-child(3)')->text(),
            'https' => $row->filter('td:nth-child(7)')->text() == "yes" ? true:false,
        );
    });



    if ($table){
        foreach ($table as $row){
            if ($row['https'] )
                Proxy::create($row);
        }

    }
});


Route::get('test/user',function (){
    $product = \App\Models\Product::find(1);
    $users = $product->users->filter(function($user) use ($product) {
        return $user->pivot->price >= $product->price;
    });

    return $users->pluck('id');;

});

Route::get('sendNotification',function (){
    $products = Product::all();
    foreach ($products as $product) {
        try {
            dispatch(new ScrapeProduct($product));
        }catch (\Exception $exception){
            Log::info($exception->getMessage());
        }

    }
});

Route::get('test-array',function (){
    $product = Product::find(1);
    $productID = $product->id;
    $lastPrice = $product->last_price;

    return $users = User::whereHas('products', function ($query) use ($productID, $lastPrice) {
        $query->where('product_id', $productID)
            ->where('price', '>', $lastPrice);
    })  ->get();

});


