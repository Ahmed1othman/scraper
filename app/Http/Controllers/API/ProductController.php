<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\ProductRequest;
use App\Http\Services\ProductService;
use App\Models\Product;
use App\Models\UserProduct;
use Illuminate\Support\Facades\Session;

class ProductController extends Controller
{

    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }


    public function index()
    {
        $products = $this->productService->getAllProducts()->orderBy('updated_at', 'desc');
        return response()->json($products);
    }
    public function userProducts()
    {
        $products = $this->productService->getAllUserProducts();
        return response()->json($products);
    }

    public function userProductsPaginated()
    {
        $user = auth()->user();
        $products = $user->products()->paginate(10);
        return response()->json($products);
    }
    public function store(ProductRequest $request){
        $user = auth()->user();
        if (!$user->subscription_status){
            return response()->json([
                    'success'=> false,
                    'message' => __('اشتراكك منتهي, من فضلك تواصل مع ادارة التطبيق لتجديد الاشتراك')
            ]);
        }
        if ($user->remainingProducts()<= 0){
            return response()->json([
                'success'=> false,
                'message' => __('تم الوصول للحد الاقصي لاضافة المنتجات ع اشتراكك تواصل مع إدارة التطبيق لزيادة عد المنتجات وترقية الاشتراك لسعة اكبر')
            ]);
        }

        $data = $request->only('url');
        $response = $this->productService->storeProduct($data);
        if ($response['code'] != 200 )
        {
            return response()->json(['message' => 'admin.invalid data,please confirm valid data and try again'], 400);
        }else
        {
            if ($response['data']){
                $product = $response['data'];
                $user = auth()->user();
                if ($user->products()->where('product_id', $product->id)->exists()) {
                    return response()->json(['message' => 'admin.product is already exists'], 200);
                }
                $user->products()->syncWithoutDetaching([
                    $product->id => [
                        'price' => $request->price,
                        'status' => $request->status,
                    ],
                ]);
                return response()->json([
                    'success'=> true,
                    'message' => 'تم إضافة المنتج الي قائمة منتجاتك بنجاح'
                ]);
            }
        }

    }

    public function update(ProductRequest $request)
    {
        $user = auth()->user();
        if (!$user->subscription_status){
            return response()->json([
                'success'=> false,
                'message' => 'اشتراكك منتهي, من فضلك تواصل مع ادارة التطبيق لتجديد الاشتراك'
            ]);
        }
         $data = $request->only('price','status');
         $userProduct = UserProduct::where('product_id', $request->product_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$userProduct)
            return response()->json([
                'success'=> false,
                'message' => 'هذا المنتج غير موجود بقائمة منتجاتك'
            ]);
        $result = $userProduct->update($data);
        if ($result)
            return response()->json([
                'success'=> true,
                'message' => 'تم تعديل المنتج'
            ]);
        return response()->json([
            'success'=> false,
            'message' => 'حدث مشكلة ما الرجاء الاتصال بمحمد عادل'
        ]);
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
    public function destroy(string $id)
    {
        $user = auth()->user();
        $userProduct = UserProduct::where('product_id', $id)
            ->where('user_id', $user->id)
            ->first();

        if ($userProduct) {
            $product = Product::find($id);
            if ($product->users->count() > 1)
            {
                $product->users()->detach($user->id);
            }
            else
            {
                $product->users()->detach();
                $product->delete();
            }

            return response()->json([
                'success'=> true,
                'message' => 'تم ازالة المنتج من قائمة منتجاتك بنجاح'
            ]);
        }else
        {
            return response()->json([
                'success'=> false,
                'message' => 'حدث مشكلة ما الرجاء الاتصال بمحمد عادل'
            ]);
        }


    }

}
