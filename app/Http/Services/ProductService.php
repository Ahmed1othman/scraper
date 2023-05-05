<?php

namespace App\Http\Services;

use App\Models\Product;

class ProductService
{

    public function getAllProducts()
    {
        return Product::all();
    }

    public function getAllUserProducts()
    {
        $user = auth()->user();
        $products = $user->products()->get();
        return $products;
    }
    public function getProductVendor(string $url): ?string
    {
        $domain = parse_url($url, PHP_URL_HOST);
        if (strpos($domain, 'amazon') !== false) {
            return 'amazon';
        } elseif (strpos($domain, 'noon') !== false) {
            return 'noon';
        } else {
            return null;
        }
    }

    public function getProductById($id)
    {
        return Product::find($id);
    }
}
