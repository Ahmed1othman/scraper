<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\ProductRequest;
use App\Http\Services\ProductService;
use App\Models\Product;
use App\Models\UserProduct;
use Illuminate\Support\Facades\Session;

class NotificationController extends Controller
{

    public function userNotification()
    {
        $user = auth()->user();
        $notification = $user->notifications()->orderBy('created_at','DESC')->paginate(10);
        return response()->json($notification);
    }

    public function show($id)
    {
        $product = $this->productService->getProductById($id);
        if (!$product) {
            return response()->json([
                'success'=> false,
                'message' => 'هذا المنتج غير موجود بقائمة منتجاتك'
            ]);
        }
        return response()->json($product);
    }

}
