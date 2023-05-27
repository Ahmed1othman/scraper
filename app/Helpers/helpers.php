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

function getAgent()
{
    static $agent = null;

    if ($agent !== null) {
        return $agent;
    }

    // get the next available proxy ordered by usage count
    $agent = \App\Models\UserAgent::where('status', 1)->inRandomOrder()->first();
    // increment the usage count of the selected proxy
    return $agent;
}

