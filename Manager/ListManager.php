<?php

namespace Dark\RedisListBundle\Manager;

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
        $value = $this->processValue($value);

        $lastId = $this->client->hlen($listName);
        $this->client->hset($listName, $lastId + 1, $value);
    }

    public function remove($listName, $id)
    {
        $lastId = $this->client->hlen($listName);

        if ($id > $lastId) {
            throw new ManagerException('Received wrong id.');
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
            throw new ManagerException('Received wrong $fromId.');
        }
        if ($valueTo > $lastId) {
            throw new ManagerException('Received wrong $toId.');
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
            throw new ManagerException(sprintf('List %s is empty', $listName));
        }

        $holes = array_values($listDump);

        if ($holes !== $listDump) {
            $this->client->del($listName);
            $this->client->hmset($listName, $holes);
        }
    }

    protected function processValue($value)
    {
        if (is_object($value)) {
            if (!method_exists($value, 'getId')) {
                throw new ManagerException('Object should have getId() method.');
            }

            $value = sprintf('%s;%s', get_class($value), $value->getId());
        }

        return $value;
    }
}