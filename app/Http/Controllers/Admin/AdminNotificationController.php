<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PriceNotification;


class AdminNotificationController extends Controller
{
    public function index()
    {
        $notifications = PriceNotification::select('price_notifications.*','users.name','users.phone','products.product_name','products.url')
            ->join('users', 'users.id', 'price_notifications.user_id')
            ->join('products','products.id','price_notifications.product_id')
            ->orderBy('created_at','DESC')
            ->paginate(10);
        return view('admin.admin_notifications.index',get_defined_vars());
    }

}
