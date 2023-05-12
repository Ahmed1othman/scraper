<?php

function getProxy()
{
    static $proxy = null;

    if ($proxy !== null) {
        return $proxy;
    }

    // get the next available proxy ordered by usage count
    $proxy = \App\Models\Proxy::where('status', 1)->orderBy('counters', 'ASC')->first();

    // increment the usage count of the selected proxy
    if ($proxy) {
        $proxy->counters++;
        $proxy->save();
    }

    return $proxy;
}
