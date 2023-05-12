<?php

namespace App\Http\Services;
use App\Models\Product;
use Exception;
use Goutte\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpClient\HttpClient;

class ScraperService
{
        public function scrape(Product $product)
        {
//            try {

                $proxy = DB::table('proxies')
                    ->inRandomOrder()
                    ->first();

                if (!$proxy) {
                    // If no proxy found, scrape without proxy
                    $client = HttpClient::create();
                } else {
                    // Create an HTTP client with the proxy
                    $client = HttpClient::create([
                        'proxy' => sprintf('http://%s:%d', $proxy->ip, $proxy->port),
                    ]);
                }

                $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3';
                $response = $client->request('GET', $product->url, [
                    'headers' => [
                        'User-Agent' => $userAgent,
                    ],
                    'timeout' => 15
                ]);

                $statusCode = $response->getStatusCode();

                if ($statusCode != 200) {
                    // If the status code is not 200, mark the proxy as failed
                    if ($proxy) {
                        DB::table('proxies')->where('id', $proxy->id)->update(['status' => 0]);
                    }
                    Log::info('proxy status false');
                }

                $html = $response->getContent();
                if ($product->platform == 'amazon') {
                    return $this->extractAmazon($html);
                } elseif ($product->platform == 'noon') {
                    return $this->extractNoon($html);
                }
//            } catch (TransportException $exception) {
//                if ($proxy) {
//                    DB::table('proxies')->where('id', $proxy->id)->update(['status' => 0]);
//                }
//                return $exception->getMessage();
//            }catch(Exception $ex){
//                return "general " . $ex->getMessage();
//            }
        }

    function extractAmazon($crawler){
        try {
            $matches = [];
            preg_match('/<span class="a-price-whole">([0-9]+)<span class="a-price-decimal">\.<\/span><\/span><span class="a-price-fraction">([0-9]+)<\/span>/', $crawler, $matches);
            $price = $matches[1] . '.' . $matches[2];
            Log::info( 'amazon' . $price);
            return $price;
        }catch (\Exception $ex){
            Log::info($ex->getMessage());
            return null;
        }
    }
    function extractNoon($crawler){
        try {
            $matches = [];
            preg_match_all('/<div class="priceNow"[^>]*>(.*?)<\/div>/s', $crawler, $matches);
            $price = preg_replace('/[^\d\.]/', '', $matches[1][0]);
            Log::info( 'noon' . $price);
            return $price;
        }catch (\Exception $ex){
            Log::info($ex->getMessage());
            return null;
        }

    }

    function getProductDetails($url){
        $client = new Client();
        $response = $client->request('GET', 'https://www.amazon.eg/dp/B08WJL2TT2');

        $html = $response->html();
        $crawler = new Crawler($html);
// Extract the product title
        $title = $crawler->filter('#productTitle')->text();

// Extract the product price
        $price = $crawler->filter('.a-price-whole')->first()->text() . '.' . $crawler->filter('.a-price-fraction')->first()->text();

        echo "Title: $title\n";
        echo "Price: $price\n";
    }

}
