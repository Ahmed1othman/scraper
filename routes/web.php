<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Jobs\ScrapeProduct;
use App\Models\Product;
use App\Models\Proxy;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Client as GuzzleClient;
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
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;


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


    $SERVER_API_KEY = env('FIREBASE_SERVER_API_KEY');
    $SENDER_ID =env('FIREBASE_SENDER_ID');
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
            Log::info('doen ');
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



Route::get('scrap-amazon-details',function (){


//    $proxy= getProxy();
//    $client = new GuzzleClient([
//        'proxy' => sprintf('%s:%d', '208.100.18.73','8800'),
//        'timeout' => 60,
//        'verify'=>false,
//        'headers' => [
//            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/112.0',
//        ],
//    ]);
//
//        $response = $client->request('GET','https://www.amazon.com/Amazon-Basics-Rechargeable-Toothbrush-Charger/dp/B08N7D5TSP/ref=d_pb_semantic_session_sims_desktop_vft_none_sccl_2_3/132-0796863-2358907?pd_rd_w=seR6P&content-id=amzn1.sym.1e1c444f-ee87-4f5a-8ebc-a5667d93ed43&pf_rd_p=1e1c444f-ee87-4f5a-8ebc-a5667d93ed43&pf_rd_r=0E28XESQTVZFRGNV4E0K&pd_rd_wg=KmTDy&pd_rd_r=a85a6c97-520b-4c38-8f71-2644e69590a9&pd_rd_i=B08N7D5TSP&psc=1');
//        return $html = $response->getBody()->getContents();
//        $crawler = new Crawler($html);
//
//        $title = $crawler->filterXPath('//h1[starts-with(@data-qa, "pdp-name-")]')->text();
//        $price = $crawler->filter('div[data-qa="div-price-now"]')->text();
//        $matches = [];
//        preg_match('/[\d\.]+/', $price, $matches);
//        $priceValue = $matches[0];
//        return [
//            'title'=>$title,
//            'price'=>$priceValue,
//        ];


    $proxy= getProxy();
    $client = HttpClient::create([
        'proxy' => sprintf('%s:%d', $proxy->ip,$proxy->port),
    ]);
    $url = 'https://www.noon.com/egypt-en/105-dual-sim-black-4mb-2g/N11046037A/p/?o=f95ba5b02f136d8c';
    $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/112.0';
    $response = $client->request('GET',$url, [
        'headers' => [
            'User-Agent' => $userAgent,
            'verify' => false
        ],
        'timeout' => 15,
    ]);
     $html = $response->getContent();

    if ($response->getStatusCode() == 200){
        $html = $response->getContent();
        $crawler = new Crawler($html);

        $title = $crawler->filter('#productTitle')->text();
        $price = $crawler->filter('.a-price-whole')->first()->text() . $crawler->filter('.a-price-fraction')->first()->text();

        if ((!empty($title) || !empty($price) ))
            return [
                'product_name'=>$title,
                'price'=>$price,
            ];
        else
            return null;
    }

})->name('proxy');



