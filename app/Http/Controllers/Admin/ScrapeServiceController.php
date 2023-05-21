<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\ProductRequest;
use App\Http\Requests\Web\ScrapeServiceRequest;
use App\Models\Product;
use App\Models\ScrapeService;
use App\Models\UserProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ScrapeServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $scrapeServices = ScrapeService::all();
        return view('admin.scrape-services.index',get_defined_vars());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.scrape-services.create');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::findOrFail($id);
        return view('admin.scrape-services.show',$product);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        $serviceConfiguration = ScrapeService::findOrFail($id);
        return view('admin.scrape-services.edit',get_defined_vars());
    }

    /**````
     * Update the specified resource in storage.
     */
    public function update(ScrapeServiceRequest $request, string $id)
    {
        $serviceConfiguration = ScrapeService::findOrFail($id);
        $serviceConfiguration->update($request->only('username','password','status'));
        $serviceConfiguration->save();
        Session::flash('success', __('admin.service configuration updated successfully'));
        return redirect()->route('scrape-services.index');
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
        return redirect()->route('scrape-services.index');
    }
}
