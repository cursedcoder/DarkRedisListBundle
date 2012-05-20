<?php

namespace Dark\RedisListBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class DarkRedisListBundle extends Bundle
{
    public function __construct()
    {
        if (!class_exists('Predis\\Client')) {
            throw new \Exception("Can't work without Predis lib.");
        }
    }
}