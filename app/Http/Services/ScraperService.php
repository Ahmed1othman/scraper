<?php

namespace App\Http\Services;
use App\Models\Product;
use App\Models\ScrapeService;
use Exception;
use Goutte\Client;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PHPUnit\Event\Telemetry\Info;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpClient\HttpClient;

class ScraperService
{
    public function scrape(Product $product)
    {
        if ($product->platform == 'amazon') {
            $scrapServiceConfiguration = ScrapeService::where('status',1)->first();
            if (!$scrapServiceConfiguration)
                return null;
            $url = 'https://proxy.scrapeops.io/v1/?api_key='.$scrapServiceConfiguration->password.'&url=' . $product->url;

            $client = new HttpBrowser(HttpClient::create(['verify_peer' => false]));

            $crawler = $client->request('GET', $url);

            // Find the elements containing the price and title
            $titleElement = $crawler->filter('#productTitle')->first();
            $priceElement = $crawler->filter('.a-price .a-offscreen')->first();
            $priceText = $priceElement->text();
            $titleText = $titleElement->text();
            // Extract numeric price
            preg_match('/[0-9.]+/', $priceText, $matches);

                $details =  [
                    'price'=>$matches[0],
//                    'stock'=>$productDetails['stock'],
                    'product_name'=>$titleText,
//                    'coupon'=>$productDetails['coupon']
                ];
                $product->update([
                    'last_price'=>$details['price'],
//                    'stock'=>$details['stock'],
//                    'coupon'=>$details['coupon'],
                'updated_at'=>Carbon::now()
                ]);
                $product->save();
                $notificationService = new NotificationService();
                $notificationService->sendPriceNotification($product);

        } elseif ($product->platform == 'noon') {

        }
    }




//    public function scrape(Product $product)
//    {
//        if ($product->platform == 'amazon') {
//            $scrapServiceConfiguration = ScrapeService::where('status',1)->first();
//            if (!$scrapServiceConfiguration)
//                return null;
//            $url = $this->extractProductCodeFromUrl($product->url);
//            $response = Http::withHeaders([
//                'Content-Type' => 'application/json',
//            ])
//                ->withBasicAuth($scrapServiceConfiguration->username, $scrapServiceConfiguration->password)
//                ->post('https://realtime.oxylabs.io/v1/queries', [
//                        'source' => 'amazon_product',
//                        'domain' => 'eg',
//                        'query' => $url,
//                        'parse' => true,
//                    ]
//                );
//            $responseData = $response->json();
//            $status = $response->status();
//            if ($status == 200){
//                $productDetails = $responseData['results'][0]['content'];
//                $details =  [
//                    'url'=>$productDetails['url'],
//                    'price'=>$productDetails['price'],
//                    'stock'=>$productDetails['stock'],
//                    'product_name'=>$productDetails['title'],
//                    'coupon'=>$productDetails['coupon']
//                ];
//                $product->update([
//                    'last_price'=>$details['price'],
//                    'stock'=>$details['stock'],
//                    'coupon'=>$details['coupon'],
//                ]);
//                $product->save();
//                $notificationService = new NotificationService();
//                $notificationService->sendPriceNotification($product);
//            }else{
//                return null;
//            }
//
//        } elseif ($product->platform == 'noon') {
//
//        }
//    }



//    function extractProductCodeFromUrl($url)
//    {
//        $pattern = '/\/dp\/([A-Z0-9]+)/';
//        preg_match($pattern, $url, $matches);
//        return $matches[1] ?? null;
//    }


//    public function scrape(Product $product)
//    {
//        $proxy = getProxy();
//        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3';
//        if ($product->platform == 'amazon') {
//
//            $client = HttpClient::create([
//                'proxy' => sprintf('%s:%d', $proxy->ip, $proxy->port),
//            ]);
//            $response = $client->request('GET', $product->url, [
//                'headers' => [
//                    'User-Agent' => $userAgent,
//                    'verify' => false
//                ],
//                'timeout' => 30,
//            ]);
//
//            if ($response->getStatusCode() == 503){
//                return redirect('google.com');
//            }
//
//                if ($response->getStatusCode() == 200) {
//                Log::info('amazon => status = 200 from scrap service');
//                $html = $response->getContent();
//                return $this->extractAmazon($html);
//            }
//        } elseif ($product->platform == 'noon') {
//            $client = HttpClient::create([
////                'proxy' => sprintf('%s:%d', $proxy->ip, $proxy->port),
//            ]);
//            $response = $client->request('GET', $product->url, [
//                'headers' => [
//                    'User-Agent' => $userAgent,
//                    'verify' => false
//                ],
//                'timeout' => 30,
//            ]);
//            if ($response->getStatusCode() == 200) {
//                $html = $response->getContent();
//                return $this->extractNoon($html);
//            }
//        }
////            } catch (TransportException $exception) {
////                if ($proxy) {
////                    DB::table('proxies')->where('id', $proxy->id)->update(['status' => 0]);
////                }
////                return $exception->getMessage();
////            }catch(Exception $ex){
////                return "general " . $ex->getMessage();
////            }
//
//    }
//
//    function extractAmazon($crawler)
//    {
////        try {
//
//        Log::info('from amazon scrap amazon function');
//        $crawler = new Crawler($crawler);
//
//        $title = $crawler->filter('#productTitle')->text();
//        $price = $crawler->filter('.a-price-whole')->first()->text() . $crawler->filter('.a-price-fraction')->first()->text();
//
//        if ((!empty($title) || !empty($price)))
//            return $price;
//        else
//            return null;
//    }
//
//    function extractNoon($crawler)
//    {
////        try {
//            $matches = [];
//            preg_match_all('/<div class="priceNow"[^>]*>(.*?)<\/div>/s', $crawler, $matches);
//            $price = preg_replace('/[^\d\.]/', '', $matches[1][0]);
//            Log::info('noon' . $price);
//            return $price;
////        } catch (\Exception $ex) {
////            Log::info($ex->getMessage());
////            return null;
////        }
//
//    }
}

