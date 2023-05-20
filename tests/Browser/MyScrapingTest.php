<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class MyScrapingTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testExample(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://www.amazon.eg/-/en/Pampers-Premium-EXTRA-Diapers-Lotion/dp/B0BF58XZR5/ref=pd_vtp_h_pd_vtp_h_sccl_3/260-3589636-6223613?pd_rd_w=qzylO&content-id=amzn1.sym.efa3bf7e-14e9-4301-8677-c7873a4c7497&pf_rd_p=efa3bf7e-14e9-4301-8677-c7873a4c7497&pf_rd_r=FGSP577N6DC6Q0562CS2&pd_rd_wg=MrSkq&pd_rd_r=deac41e7-7468-45d3-9b74-6271c6f95ab0&pd_rd_i=B0BF58XZR5&th=1')
                ->waitFor('#productTitle')
                ->storeSource('page.html');
        });

        $html = file_get_contents('page.html');
    }
}
