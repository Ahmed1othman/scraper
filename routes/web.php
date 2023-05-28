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
        Route::get('/',function (){
            return redirect()->route('dashboard');
        });
        Route::get('/dashboard',[AdminDashboardController::class,'index'])->name('dashboard');
        Route::resource('users',UserController::class);
        Route::post('users.change-password',[UserController::class,'changePassword'])->name('users.change.password');
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





