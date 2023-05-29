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
    public function store(ProductRequest $request){
        $user = auth()->user();
        if (!$user->subscription_status){
            return response()->json(['message' => __('admin.year subscription is expired, contact admins to renew')], 410);
        }
        if ($user->remainingProducts()<= 0){
            return response()->json(['message' => __('admin.you reach the maximum number of products for your subscription')], 409);
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
                return response()->json(['message' => 'success'], 200);
            }
        }

    }

    public function update(ProductRequest $request)
    {
        $user = auth()->user();
        if (!$user->subscription_status){
            return response()->json(['message' => __('admin.year subscription is expired, contact admins to renew')], 410);
        }
        if ($user->remainingProducts()<= 0){
            return response()->json(['message' => __('admin.you reach the maximum number of products for your subscription')], 409);
        }
         $data = $request->only('price','status');
         $userProduct = UserProduct::where('product_id', $request->product_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$userProduct)
            return response()->json(
                [
                    'message' => 'you have not this product',
                    'code' => '404',
                ],'200');
        $result = $userProduct->update($data);
        if ($result)
            return response()->json(
                [
                    'message' => 'success',
                    'code' => '200',
                ],'200');
        return response()->json(
            [
                'message' => 'an error',
                'code' => '404',
            ],'200');
    }
    public function show($id)
    {
        $product = $this->productService->getProductById($id);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
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

            return response()->json(
                [
                    'message' => 'success',
                    'code' => '200',
                ],'200');
        }else
        {
            return response()->json(
                [
                    'message' => 'error',
                    'code' => '402',
                ],'200');
        }


    }

}
