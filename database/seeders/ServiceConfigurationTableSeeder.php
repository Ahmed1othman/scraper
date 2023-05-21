<?php
namespace Database\Seeders;
use App\Models\Proxy;
use App\Models\ScrapeService;
use Illuminate\Database\Seeder;


class ServiceConfigurationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ScrapeService::create([
            'username'=>'Ahmed',
            'password'=>'Ahmed_2023'
        ]);
    }
}
