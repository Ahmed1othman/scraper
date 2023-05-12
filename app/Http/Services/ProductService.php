<?php

namespace App\Http\Services;

use App\Models\Product;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

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


    public function getProductById($id)
    {
        return Product::find($id);
    }


    public function storeProduct($data){
        $platform = $this->getProductVendor($data['url']);
        if ($platform!=null){
            $result = [];
            if ($platform == 'noon'){
                 $result = $this->getNoonProductDetails($data['url']);
            }elseif ($platform=='amazon'){
                 $result = $this->getAmazonProductDetails($data['url']);
            }

            if ($result==null)
                return ['code' => '405','message'=> 'wrong url, please check data and try again'];
            else{
                $data['product_name'] = $result['product_name'];
                $data['platform'] = $platform;

                $product =  Product::create($data);
                return [
                  'code'=> 200,
                  'data'=>$product,
                ];
            }
        }else{
            return ['code' => '410','message'=> 'wrong platform'];
        }
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
    function getNoonProductDetails($url){
//        $proxy= getProxy();
        $client = HttpClient::create([
//        'proxy' => sprintf('%s:%d', $proxy->ip,$proxy->port),
        ]);

        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3';
        $response = $client->request('GET','https://www.noon.com/egypt-ar/fresh-fabric-cleaner-4l/N28892952A/p/?o=ef88374cabba23ff' , [
            'headers' => [
                'User-Agent' => $userAgent,
            ],
            'timeout' => 15,
        ]);


        if ($response->getStatusCode() == 200){

            $html = $response->getContent();
            $crawler = new Crawler($html);

            $title = $crawler->filterXPath('//h1[starts-with(@data-qa, "pdp-name-")]')->text();
            $price = $crawler->filter('div[data-qa="div-price-now"]')->text();
            $matches = [];
            preg_match('/[\d\.]+/', $price, $matches);
            $priceValue = $matches[0];

            if ((!empty($title) || !empty($priceValue) ))
                return [
                    'product_name'=>$title,
                    'price'=>$priceValue,
                ];
            else
                return null;
        }

    }
    function getAmazonProductDetails($url){
//        try {

        //dd($url);

        $proxy= getProxy();
        $client = HttpClient::create([
            'proxy' => sprintf('%s:%d', $proxy->ip,$proxy->port),
        ]);

        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3';
        $response = $client->request('GET',$url, [
            'headers' => [
                'User-Agent' => $userAgent,
                'verify' => false
            ],
            'timeout' => 15,
        ]);

        if ($response->getStatusCode() == 200){
            $html = $response->getContent();
            $crawler = new Crawler($html);

            $title = $crawler->filter('#productTitle')->text();
            $price = $crawler->filter('.a-price-whole')->first()->text() . $crawler->filter('.a-price-fraction')->first()->text();

            if ((!empty($title) || !empty($price) ))
            return [
                'product_name'=>$title,
                'price'=>$price,
            ];
            else
                return null;
        }
    }
}
