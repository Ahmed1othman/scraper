<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ScrapeServiceController;
use App\Http\Controllers\Admin\UserController;
use App\Jobs\ScrapeProduct;
use App\Models\Product;
use App\Models\Proxy;
use App\Models\User;
use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Symfony\Component\DomCrawler\Crawler;
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
        Route::resource('admin-products',AdminProductController::class);
        Route::resource('scrape-services',ScrapeServiceController::class);
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

});


Route::get('test/user',function (){
    $product = \App\Models\Product::find(1);
    $users = $product->users->filter(function($user) use ($product) {
        return $user->pivot->price >= $product->price;
    });
    return $users->pluck('id');
});

Route::get('sendNotification',function (){
    $products = Product::orderBy('updated_at','ASC')->get();
    if ($products->count() > 0){
        foreach ($products as $product) {
            try {
                dispatch(new ScrapeProduct($product));
                Log::info('done');
            }catch (\Exception $exception){
                Log::info($exception->getMessage());
            }
        }
    }







    // try {
    //     // Determine the queue name based on the index
    //     $queueName = $queueNames[$index % count($queueNames)];

    //     // Dispatch the job to the specified queue
    //     Queue::pushOn($queueName, new ScrapeProduct($product));

    //     Log::info('Job dispatched for product ID: ' . $product->id . ' to queue: ' . $queueName);
    // } catch (\Exception $exception) {
    //     Log::info($exception->getMessage());
    // }

});
//     $products = Product::all();
//     if ($products->count() > 0)
//         foreach ($products as $product) {
//             //make random interval between scrapping request
// //            $minDelay = '1';
// //            $maxDelay = '30';
// //            $delay = rand($minDelay, $maxDelay);
// //            sleep($delay);
//             try {
//                 dispatch(new ScrapeProduct($product));
//                 Log::info('done');
//             }catch (\Exception $exception){
//                 Log::info($exception->getMessage());
//             }
//         }
// });

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
    $url = 'https://www.noon.com/egypt-en/swimwear-burkinis/Z229FB29E22590DDFC8FAZ/p/?o=z229fb29e22590ddfc8faz-1';
    $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3';

    $httpClient = HttpClient::create([
        'headers' => [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.93 Safari/537.36',
            'Referer' => 'https://www.google.com.eg'
        ]
    ]);
    $client = new Client($httpClient);

    $crawler = $client->request('GET', $url);
    return $client->getResponse();

    return $productTitle = $crawler->filter('.priceNow')->text();













    $response = Http::withHeaders([
        'Content-Type' => 'application/json',
    ])
        ->withBasicAuth('Ahmed', 'Ahmed_2023')
        ->post('https://realtime.oxylabs.io/v1/queries', [
                'source' => 'amazon_product',
                'domain' => 'eg',
                'query' => 'B08WJL2TT2',
                'parse' => true,
            ]
        );

    $responseData = $response->json();

    $status = $response->status();

//return $responseData->;
    $productDetails = $responseData['results'][0]['content'];
    return [
        'url'=>$productDetails['url'],
        'price'=>$productDetails['price'],
        'stock'=>$productDetails['stock'],
        'title'=>$productDetails['url']
    ];



    $params = [
        'source' => 'amazon',
        'url' => 'https://www.amazon.co.uk/dp/B08Y6Z944Q/',
        'parse' => true,
    ];

    $response = Http::withBasicAuth('Ahmed', 'Ahmed_2023')
        ->withHeaders([
            'Content-Type' => 'application/json',
        ])
        ->post('https://realtime.oxylabs.io/v1/queries', $params);

    if ($response->failed()) {
        echo 'Error: ' . $response->body();
    } else {
        echo $response->body();
    }




    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://ipv4.icanhazip.com",
        CURLOPT_PROXY => "https://ahmedothman:k935AtEYFfLNcqI2@proxy.packetstream.io:31111",
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_RETURNTRANSFER => 1,
    ));
    $response = curl_exec($curl);
    dd($response);

    $payload = json_encode(
        array(
            "apiKey"
            =>
                "eb7e2c841bf8b5f54509acf2ff76da67",
            "url"
            =>
                "https://www.amazon.com/dp/B0B57LMT7Y/ref=sr_1_7?keywords=pet%2Bsupplies&pd_rd_r=b4495182-0873-451a-9f76-e00285b3d0c6&pd_rd_w=VFbUt&pd_rd_wg=ki5jA&pf_rd_p=31b6795c-1fec-44bb-a8df-44b7c120294b&pf_rd_r=99QH6MS8AGKNH4ZZDNS3&qid=1684322841&sr=8-7&th=1&language=en_US&currency=EGP"
        ) ); $ch = curl_init(); curl_setopt( $ch, CURLOPT_URL,
        "https://async.scraperapi.com/jobs"
    ); curl_setOpt( $ch, CURLOPT_POST,
        1
    ); curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload ); curl_setopt( $ch, CURLOPT_HTTPHEADER,
        array(
            "Content-Type:application/json"
        ) ); curl_setopt( $ch, CURLOPT_RETURNTRANSFER,
        TRUE
    ); $response = curl_exec( $ch ); curl_close( $ch ); print_r( $response );

    dd($response);

//    $minDelay = '1';
//    $maxDelay = '20';
//    $delay = rand($minDelay, $maxDelay);
//    sleep($delay);
    $goutteClient = new Client();
    $proxy = 'tcp://154.38.28.230:8800'; // replace with your proxy URL;
//    $proxy = getProxy();
    $url = 'https://www.amazon.eg/-/en/Pampers-Premium-EXTRA-Diapers-Lotion/dp/B0BF5DP9RN/ref=d_pd_sim_sccl_2_2/260-3589636-6223613?pd_rd_w=lExIe&content-id=amzn1.sym.cfa92291-ac91-4d69-8d3c-f411d3e825ea&pf_rd_p=cfa92291-ac91-4d69-8d3c-f411d3e825ea&pf_rd_r=WP5FYVE6QNFR9F4MG7QT&pd_rd_wg=Uniqe&pd_rd_r=f3a63453-a75b-456f-baf1-cc0594a48c85&pd_rd_i=B0BF5DP9RN&psc=1';


//    $crawler = $goutteClient->request('GET', $url,['proxy'=>''.$proxy->ip .':'. $proxy->port.'']);
    $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/112.0';

    $crawler = $goutteClient->request('GET', $url,['User-Agent'=>$userAgent]);
//    $delay = rand($minDelay, $maxDelay);
//    sleep($delay);

    $captcha = $crawler->filter('#captchacharacters')->each(function ($node) {
        return $node->text(); // or $node->attr('id') to get the button's ID
    });

//    $status = $goutteClient->getResponse()->getStatusCode();
//    dump($goutteClient->getResponse());
//    dump($status);
//    dd(count($buttons));
    if (count($captcha))
    {
        return $goutteClient->getResponse();
    }

    // Check if the button is found
//    if ($buttons->count() > 0) {
//        // Click the button
//        $form = $buttons[1]->form();
//        $crawler = $goutteClient->submit($form);
//    }
    $status = $goutteClient->getResponse()->getStatusCode();
    $title = $crawler->filter('#productTitle')->text();
    dd($title);










    $minDelay = '1';
    $maxDelay = '30';
    $delay = rand($minDelay, $maxDelay);
    sleep($delay);
    $proxy = 'tcp://154.38.28.230:8800'; // replace with your proxy URL;
    $newClient = new \Goutte\Client();

    $goutteClient = new Client();
    $proxyOptions = [
        'proxy' => 'http://username:password@proxy_ip:proxy_port',
        // Additional proxy options can be added here if needed
    ];
    $guzzleClient = new GuzzleClient($proxyOptions);

    $url = 'https://www.amazon.eg/-/en/Pampers-Premium-EXTRA-Diapers-Lotion/dp/B0BF5DP9RN/ref=d_pd_sim_sccl_2_2/260-3589636-6223613?pd_rd_w=lExIe&content-id=amzn1.sym.cfa92291-ac91-4d69-8d3c-f411d3e825ea&pf_rd_p=cfa92291-ac91-4d69-8d3c-f411d3e825ea&pf_rd_r=WP5FYVE6QNFR9F4MG7QT&pd_rd_wg=Uniqe&pd_rd_r=f3a63453-a75b-456f-baf1-cc0594a48c85&pd_rd_i=B0BF5DP9RN&psc=1';
    $crawler = $newClient->request('GET', $url);
    $status = $newClient->getResponse()->getStatusCode();
    $title = $crawler->filter('#productTitle')->text();
    dd($title);
    $crawler->filter('')->each(function ($node) use ($crawler) {
        dd($node);
    });





    $proxy= getProxy();
    $userAgent= getAgent();
    $client = HttpClient::create([
//        'proxy' => sprintf('%s:%d', $proxy->ip,$proxy->port),
    ]);

    //make random interval between scrapping request
    $minDelay = '1';
    $maxDelay = '30';
    $delay = rand($minDelay, $maxDelay);
    sleep($delay);
    $url = 'https://www.amazon.eg/-/en/Pampers-Premium-EXTRA-Diapers-Lotion/dp/B0BF5DP9RN/ref=d_pd_sim_sccl_2_2/260-3589636-6223613?pd_rd_w=lExIe&content-id=amzn1.sym.cfa92291-ac91-4d69-8d3c-f411d3e825ea&pf_rd_p=cfa92291-ac91-4d69-8d3c-f411d3e825ea&pf_rd_r=WP5FYVE6QNFR9F4MG7QT&pd_rd_wg=Uniqe&pd_rd_r=f3a63453-a75b-456f-baf1-cc0594a48c85&pd_rd_i=B0BF5DP9RN&psc=1';
//    $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/112.0';
    $response = $client->request('GET',$url, [
        'headers' => [
            'User-Agent' => $userAgent->agent,
            'verify' => false
        ],
        'timeout' => 30,
    ]);
    return $html = $response->getContent();
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



