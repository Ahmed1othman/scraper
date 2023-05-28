<?php

namespace App\Console\Commands;

use App\Jobs\ScrapeProxiesJob;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CheckSubscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:subscription';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check subscription expiration date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $currentDate = Carbon::now();
        $users = User::normalUsers()->where('subscription_expiration_date', '<', $currentDate->endOfDay())->pluck('id');
        User::whereIn('id',$users)->update(['subscription_status' => false]);
//        dispatch(new ScrapeProxiesJob());
    }
}
