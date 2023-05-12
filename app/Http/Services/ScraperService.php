<?php

namespace App\Http\Services;
use App\Models\Product;
use Exception;
use Goutte\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PHPUnit\Event\Telemetry\Info;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpClient\HttpClient;

class ScraperService
{
    public function scrape(Product $product)
    {
        $proxy = getProxy();
        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3';
        if ($product->platform == 'amazon') {

            $client = HttpClient::create([
                'proxy' => sprintf('%s:%d', $proxy->ip, $proxy->port),
            ]);
            $response = $client->request('GET', $product->url, [
                'headers' => [
                    'User-Agent' => $userAgent,
                    'verify' => false
                ],
                'timeout' => 30,
            ]);

            if ($response->getStatusCode() == 200) {
                Log::info('amazon => status = 200 from scrap service');
                $html = $response->getContent();
                return $this->extractAmazon($html);
            }
        } elseif ($product->platform == 'noon') {
            $client = HttpClient::create([
                'proxy' => sprintf('%s:%d', $proxy->ip, $proxy->port),
            ]);
            $response = $client->request('GET', $product->url, [
                'headers' => [
                    'User-Agent' => $userAgent,
                    'verify' => false
                ],
                'timeout' => 30,
            ]);
            if ($response->getStatusCode() == 200) {
                $html = $response->getContent();
                return $this->extractNoon($html);
            }
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

    function extractAmazon($crawler)
    {
//        try {

        Log::info('from amazon scrap amazon function');
        $crawler = new Crawler($crawler);

        $title = $crawler->filter('#productTitle')->text();
        $price = $crawler->filter('.a-price-whole')->first()->text() . $crawler->filter('.a-price-fraction')->first()->text();

        if ((!empty($title) || !empty($price)))
            return $price;
        else
            return null;
    }



//            $matches = [];
//            preg_match('/<span class="a-price-whole">([0-9]+)<span class="a-price-decimal">\.<\/span><\/span><span class="a-price-fraction">([0-9]+)<\/span>/', $crawler, $matches);
//            $price = $matches[1] . '.' . $matches[2];
//            Log::info( 'amazon' . $price);
//        Log::info('from the end of amazon scrap');
//            return $price;
//        }catch (\Exception $ex){
//            Log::info($ex->getMessage());
//            return null;
//        }

    function extractNoon($crawler)
    {
//        try {
            $matches = [];
            preg_match_all('/<div class="priceNow"[^>]*>(.*?)<\/div>/s', $crawler, $matches);
            $price = preg_replace('/[^\d\.]/', '', $matches[1][0]);
            Log::info('noon' . $price);
            return $price;
//        } catch (\Exception $ex) {
//            Log::info($ex->getMessage());
//            return null;
//        }

    }
}

