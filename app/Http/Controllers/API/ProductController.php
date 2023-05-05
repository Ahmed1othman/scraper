<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Services\ProductService;
use App\Models\Product;
use http\Env\Response;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }


    public function index()
    {
        $products = $this->productService->getAllProducts();
        return response()->json($products);
    }


    public function userProducts()
    {
        $products = $this->productService->getAllUserProducts();
        return response()->json($products);
    }
    public function store(ProductRequest $request){

        $platform = $this->productService->getProductVendor($request->url);
        if ($platform == null)
            return response()->json(['error' => 'Invalid URL'], 400);


        $product = Product::updateOrCreate(
            ['url' => $request->url],
            ['product_name' => $request->product_name, 'platform' => $platform]
        );
        $user = auth()->user();
        $user->products()
            ->syncWithoutDetaching([
                $product->id => [
                    'price' => $request->price,
                    'status' => $request->status,
                ]
            ]);

        return response()->json(['message' => 'success'], 200);
    }
    public function show($id)
    {
        $product = $this->productService->getProductById($id);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        return response()->json($product);
    }


}
