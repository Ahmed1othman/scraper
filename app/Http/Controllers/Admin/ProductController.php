<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\ProductRequest;
use App\Http\Services\ProductService;
use App\Models\Product;
use App\Models\UserProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ProductController extends Controller
{

    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $products = $user->products;

        return view('admin.products.index',get_defined_vars());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {

        $data = $request->only('url');
        $response = $this->productService->storeProduct($data);
        if ($response['code'] != 200 )
        {
            Session::flash('danger', __('admin.invalid data,please confirm valid data and try again'));
            return back()->withInput();
        }else
        {
            if ($response['data']){
                $product = $response['data'];
                $user = auth()->user();
                $user->products()
                    ->attach($product,[
                        'price'=>$request->price,
                        'status' => $request->status,
                    ]);
                Session::flash('success', __('admin.product added successfully'));
                return redirect()->route('products.index');
            }
        }



    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::findOrFail($id);
        return view('admin.products.show',$product);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = auth()->user();
        $product = Product::findOrFail($id);
        $userProduct = UserProduct::where('product_id', $product->id)
            ->where('user_id', $user->id)
            ->first();
        if (!$userProduct)
            return abort(404);
        return view('admin.products.edit',get_defined_vars());
    }

    /**````
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, string $id)
    {
        $user = auth()->user();
        $data = $request->only('price','status');
        $userProduct = UserProduct::where('product_id', $id)
            ->where('user_id', $user->id)
            ->first();
        $userProduct->update($data);
        Session::flash('success', __('admin.product updated successfully'));
//        return redirect()->route('products.edit',$id);
        return redirect()->route('products.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = auth()->user();
        $product = Product::findOrFail($id);
        $userProduct = UserProduct::where('product_id', $product->id)
            ->where('user_id', $user->id)
            ->first();

        if ($userProduct) {
            if ($product->users->count() > 1)
            {
                $product->users()->detach($user->id);
            }
            else
            {
                $product->users()->detach();
                $product->delete();
            }

            Session::flash('success', __('admin.product deleted successfully'));
            return redirect()->route('products.index');
        }else
        {
            Session::flash('error', __('admin.product deleted successfully'));
            return redirect()->route('products.index');
        }

    }
}
