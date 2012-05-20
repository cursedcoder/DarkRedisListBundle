<?php

namespace Dark\RedisListBundle\Repository;

interface Repository
{
    public function process($elements);
}