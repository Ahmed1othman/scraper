<?php

namespace App\Jobs;

use App\Http\Services\ScraperService;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ScrapeProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public Product $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function handle()
    {
        $scraper = new ScraperService();
        $price = $scraper->scrape($this->product);

        if ($price != null){
            $newPrice = floatval($price);
            $this->product->update(['last_price' => $newPrice]);
            Log::info('before notification job');
            dispatch(new SendPriceNotificationJob($this->product));

        }
    }
}
