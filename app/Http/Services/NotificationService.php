<?php

namespace App\Http\Services;

use App\Models\PriceNotification;
use App\Models\Product;
use Illuminate\Support\Facades\Http;

class NotificationService
{
    public function snedPriceNotification(Product $product): void
    {
        $users = $product->users->filter(function($user) use ($product) {
            return $user->pivot->price >= $product->price;
        });
        $this->storeDatabaseNotification($product,$users);
        $this->sendRealTimeNotification($product,$users);
    }

    function sendRealTimeNotification($product,$users): void
    {
        $tokens = $users->pluck('fcm_token');

        $title = $product->product_name . 'تنبيه تحديث في سعر المنتج : ';
        $SERVER_API_KEY = 'AAAAgzxdbFg:APA91bHpS8bkZRzv9EfT4U1QVmkIoCnUDKVJP5fDYEcsIkz2hoB73og1ooWcGj2JUpdN2KRRttcBUfrt67Im6CqrwHd7sV-fo4hW6MV7kPgSrg_lFqJQcNnJVSlyIoCzPY0IzxOB0RbQ';
        $SENDER_ID = '563653471320';
        $data = [
            "registration_ids" => $tokens,
            "notification" => [
                "title" => $title,
                "body" => "تم تحديث سعر المنتج :  " .$product->product_name . " ليصبح : " . $product->last_price,
                "sound" => "default"
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
