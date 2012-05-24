<?php

namespace Dark\RedisListBundle\Collector;

interface CollectorInterface
{
    function process($elements);
}