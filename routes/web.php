<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ScrapeServiceController;
use App\Http\Controllers\Admin\UserController;

use App\Jobs\ScrapeProduct;
use App\Models\Product;

use App\Models\ScrapeService;


use Goutte\Client;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Sunra\PhpSimple\HtmlDomParser;
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
        Route::get('/',function (){
            return redirect()->route('dashboard');
        });
        Route::get('/dashboard',[AdminDashboardController::class,'index'])->name('dashboard');
        Route::resource('users',UserController::class);
        Route::post('users.change-password',[UserController::class,'changePassword'])->name('users.change.password');
        Route::resource('admin-products',AdminProductController::class);
        Route::resource('admin-notifications',AdminNotificationController::class);
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
});


Route::get('noon',function () {
        $scrapServiceConfiguration = ScrapeService::where('status',1)->first();
        if (!$scrapServiceConfiguration)
            return null;
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])
            ->withBasicAuth($scrapServiceConfiguration->username, $scrapServiceConfiguration->password)
            ->post('https://realtime.oxylabs.io/v1/queries', [
                    'source' => 'amazon_product',
                    'domain' => 'eg',
                    'query' => 'B0C155LQMX',
                    'parse' => true,
                ]
            );
        $responseData = $response->json();
        $status = $response->status();
        $productDetails = $responseData['results'][0]['content'];
        return $details =  [
            'url'=>$productDetails['url'],
            'price'=>$productDetails['price'],
            'stock'=>$productDetails['stock'],
            'product_name'=>$productDetails['title'],
            'coupon'=>$productDetails['coupon']
        ];

});



use Symfony\Component\BrowserKit\HttpBrowser;

Route::get('amazon', function () {

    $products = Product::all();
    foreach ($products as $product) {
        $url = 'https://proxy.scrapeops.io/v1/?api_key=7b916427-29e1-486e-b80a-7f168e9d0224&url=' . $product->url;

        $client = new HttpBrowser(HttpClient::create(['verify_peer' => false]));

        $crawler = $client->request('GET', $url);
        // Find the elements containing the price and title
        $titleElement = $crawler->filter('#productTitle')->first();
        $priceElement = $crawler->filter('.a-price .a-offscreen')->first();
        $priceText = $priceElement->text();
        $titleText = $titleElement->text();
        // Extract numeric price
        preg_match('/[0-9.]+/', $priceText, $matches);
        dump($matches[0]);
        dump($titleText);
    }
});











