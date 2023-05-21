<?php

namespace App\Console\Commands;

use App\Jobs\ScrapeProduct;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ScrapeProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:scrape';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $products = Product::all();
        foreach ($products as $product) {
            try {
                dispatch(new ScrapeProduct($product));
            }catch (\Exception $exception){
                Log::info($exception->getMessage());
            }
        }
        $this->info('All products scraped successfully!');
    }
}
