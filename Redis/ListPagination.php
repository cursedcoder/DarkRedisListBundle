<?php

namespace Dark\RedisListBundle\Redis;

use Predis\Client;

class ListPagination
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getPage($hashName, $page, $perPage)
    {
        $count = $this->client->hlen($hashName);

        $range = range($count - $page * $perPage, $count);
        $elements = $this->client->hmget($hashName, $range);
    }

}