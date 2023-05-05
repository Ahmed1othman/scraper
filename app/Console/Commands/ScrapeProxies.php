<?php

namespace App\Console\Commands;

use App\Jobs\ScrapeProxiesJob;
use Illuminate\Console\Command;

class ScrapeProxies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:proxies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update proxies';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        dispatch(new ScrapeProxiesJob());
    }
}
