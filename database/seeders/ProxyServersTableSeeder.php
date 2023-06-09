<?php
namespace Database\Seeders;
use App\Models\Proxy;
use Illuminate\Database\Seeder;


class ProxyServersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ips = [
            '154.9.32.40:8800',
            '154.37.251.18:8800',
            '154.38.28.146:8800',
            '154.9.32.156:8800',
            '179.43.132.184:8800',
            '179.43.132.232:8800',
            '154.9.32.61:8800',
            '154.37.251.254:8800',
            '154.37.251.153:8800',
            '154.38.28.230:8800',
            '154.9.32.83:8800',
            '154.37.248.17:8800',
            '179.43.132.221:8800',
            '154.37.248.168:8800',
            '154.37.248.96:8800',
            '179.43.129.48:8800',
            '154.37.248.91:8800',
            '154.38.28.219:8800',
            '154.37.251.102:8800',
            '179.43.129.108:8800',
            '154.38.28.183:8800',
            '179.43.129.131:8800',
            '179.43.129.165:8800',
            '179.43.129.205:8800',
            '179.43.132.83:8800'
        ];

        foreach ($ips as $ip) {
            Proxy::create([
                'ip' => explode(':', $ip)[0],
                'port' => explode(':', $ip)[1],
                'status' => 1,
            ]);
        }
    }
}
