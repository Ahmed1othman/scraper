<?php

namespace App\Jobs;

use App\Models\Proxy;
use Goutte\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ScrapeProxiesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $client = new Client();

        $url = 'https://free-proxy-list.net/';

        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3';
        $crawler =  $client->request('GET', $url, [
            'headers' => [
                'User-Agent' => $userAgent,
            ],
        ]);

        $rows = array();
        $table = $crawler->filter('.table-striped tbody tr')->each(function ($row) {
            return  array(
                'ip' => $row->filter('td:nth-child(1)')->text(),
                'port' => $row->filter('td:nth-child(2)')->text(),
                'code' => $row->filter('td:nth-child(3)')->text(),
                'https' => $row->filter('td:nth-child(7)')->text() == "yes" ? true:false,
            );
        });



        if ($table){
            foreach ($table as $row){
                if ($row['https'])
                    Proxy::create($row);
            }

        }

    }
}
