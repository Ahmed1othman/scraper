<?php

namespace App\Http\Services;

use App\Models\PriceNotification;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use function PHPUnit\Framework\isEmpty;

class NotificationService
{
    public function snedPriceNotification(Product $product): void
    {
        $productID = $product->id;
        $lastPrice = $product->last_price;
        if ($lastPrice > 0) {
            $users = User::whereHas('products', function ($query) use ($productID, $lastPrice) {
                $query->where('product_id', $productID)
                    ->where('price', '>', $lastPrice)
                    ->where('status', 1);
            })->get();
            Log::info($users);
            if (!$users->isEmpty()) {
//            $this->storeDatabaseNotification($product,$users);
                $this->sendRealTimeNotification($product, $users);
            }
        }
    }

    function sendRealTimeNotification($product,$users): void
    {
        $tokens = $users->pluck('fcm_token');
        Log::info('tokens : ' . $tokens);
        $title = $product->product_name . 'تنبيه تحديث في سعر المنتج : ';


        $SERVER_API_KEY = env('FIREBASE_SERVER_API_KEY');
        $SENDER_ID =env('FIREBASE_SENDER_ID');
        $data = [
            "registration_ids" => $tokens,
            "notification" => [
                "title" => $title,
                "body" => "تم تحديث سعر المنتج :  " .$product->product_name . " ليصبح : " . $product->last_price,
                'android_channel_id' => 'x-tracker-id',
                'sound' => 'notification',
            ],
            "data" => [
                "product_id" => $product->id
            ]
        ];
        $response = Http::withHeaders([
            'Authorization' => 'key=' . $SERVER_API_KEY,
            'SenderId' => $SENDER_ID,
            'Content-Type' => 'application/json',
        ])->post('https://fcm.googleapis.com/fcm/send', $data);
    }

    function storeDatabaseNotification(Product $product,$users): void
    {
        $ids = $product->pluck('id');
        Log::info('ids : ' .$ids);
        if (!isEmpty($ids))
            foreach ($ids as $id)
        {
            $notification = new PriceNotification();
            $notification->user_id = $id;
            $notification->product_id = $product->id;
            $notification->message = "new price alert for product " .$product->name . "is: " . $product->last_price;
            $notification->save();
        }

    }
}
