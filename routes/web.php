<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\RoleController;
use App\Jobs\ScrapeProduct;
use App\Models\Product;
use App\Models\Proxy;
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
        Route::resource('products',ProductController::class);
    });


    Route::get('test/amazon',function (){

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
//    Route::get('test/view',function (){
//       return view('scraper') ;
//    });


});


//Route::get('test/socket-send',function (){
//    event(new App\Events\AmazonPriceNotify('Hello World'));
//});



//Route::get('test/socket-receive',function (){
//    return view('socket');
//});


Route::resource('roles',RoleController::class);


Route::get('/save-token', function (Request $request) {
    auth()->user()->update(['fcm_token'=>$request->token]);
    return response()->json(['token saved successfully.']);
})->name('save-push-notification-token');

Route::get('/message', function () {


    $SERVER_API_KEY = 'AAAAgzxdbFg:APA91bHpS8bkZRzv9EfT4U1QVmkIoCnUDKVJP5fDYEcsIkz2hoB73og1ooWcGj2JUpdN2KRRttcBUfrt67Im6CqrwHd7sV-fo4hW6MV7kPgSrg_lFqJQcNnJVSlyIoCzPY0IzxOB0RbQ';
    $SENDER_ID = '563653471320';
    $token_1 = 'e2V33odFv2af9RQuZ07-ka:APA91bHk2ptj2-wSjtlWgdsBBL76lk9tAG5gogbpHF8OHzGcBhaQ1Two8bZ8-Myxu9j5YqoazMoGCjJyDDRqtIk4ymjjDmxgrpI0ibVJjpzfCJHO-KKO1jizTPr6uqLsFDlBYowY7JZM';

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
            return "done";
        }catch (\Exception $exception){
            Log::info($exception->getMessage());
        }

    }
});
