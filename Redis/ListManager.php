<?php

namespace Dark\RedisListBundle\Redis;

use Dark\RedisListBundle\Redis\ListException;

use Predis\Client;

class ListManager
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function push($listName, $value)
    {
        $lastId = $this->client->hlen($listName);
        $this->client->hset($listName, $lastId + 1, $value);
    }

    public function remove($listName, $id)
    {
        $lastId = $this->client->hlen($listName);

        if ($id > $lastId) {
            throw new ListException('Recieved wrong id.');
        }

        for ($i = $id; $i < $lastId && $lastId != $id; $i++) {
            $this->swap($listName, $i, $i + 1);
        }

        $this->client->hdel($listName, $lastId);
    }

    public function swap($listName, $fromId, $toId)
    {
        $lastId = $this->client->hlen($listName);

        $valueFrom = $this->client->hget($listName, $fromId);
        $valueTo = $this->client->hget($listName, $toId);

        if ($valueFrom > $lastId) {
            throw new ListException('Recieved wrong $fromId.');
        }
        if ($valueTo > $lastId) {
            throw new ListException('Recieved wrong $toId.');
        }

        $this->client->hset($listName, $fromId, $valueTo);
        $this->client->hset($listName, $toId, $valueFrom);
    }

    public function move($fromList, $toList, $id)
    {
        $value = $this->client->hget($fromList, $id);

        $this->add($toList, $value);
        $this->remove($fromList, $id);
    }

    public function repair($listName)
    {
        $previousValue = null;
        $listDump = $this->client->hgetall($listName);

        if (count($listDump) == 0) {
            throw new ListException(sprintf('List %s is empty', $listName));
        }

        $holes = array_values($listDump);

        if ($holes !== $listDump) {
            $this->client->del($listName);
            $this->client->hmset($listName, $holes);
        }
    }
}