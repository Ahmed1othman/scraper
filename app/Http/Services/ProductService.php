<?php

namespace App\Http\Services;

class ProductService
{
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
}
