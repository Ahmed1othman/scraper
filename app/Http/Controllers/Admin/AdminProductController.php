<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\ProductRequest;
use App\Models\Product;
use App\Models\UserProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::withCount('users')->paginate(3);
        return view('admin.admin_products.index',get_defined_vars());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.admin_products.create');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::findOrFail($id);
        return view('admin.admin_products.show',$product);
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
        return view('admin.admin_products.edit',get_defined_vars());
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
        return redirect()->route('admin-products.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = auth()->user();
        if ($user->hasRole('Super Admin')){
            $product = Product::findOrFail($id);
            $product->delete();
        }

        Session::flash('error', __('admin.product deleted successfully'));
        return redirect()->route('admin-products.index');
    }
}
