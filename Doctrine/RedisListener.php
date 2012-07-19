<?php

namespace Dark\RedisListBundle\Doctrine;

use Doctrine\ORM\Event\LifecycleEventArgs;

class RedisListener
{
    private $redisManager;

    public function __construct($redisManager)
    {
        $this->redisManager = $redisManager;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (method_exists($entity, 'setRedisFields')) {
            $rm = $this->getRedisManager();
            $rm->save($entity);
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (method_exists($entity, 'setRedisFields')) {
            $rm = $this->getRedisManager();
            $rm->save($entity);
        }
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (method_exists($entity, 'getRedisFields')) {
            $rm = $this->getRedisManager();
            $rm->pull($entity);
        }
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (method_exists($entity, 'setRedisFields')) {
            $rm = $this->getRedisManager();
            $rm->remove($entity);
        }
    }

    /**
     * @return \Dark\RedisListBundle\Manager\Manager
     */
    private function getRedisManager()
    {
        return $this->redisManager;
    }
}
