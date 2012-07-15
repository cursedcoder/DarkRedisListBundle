<?php

namespace Dark\RedisListBundle\Manager;

use Predis\Client;

class Manager
{
    private $client;

    public function __consturct(Client $client)
    {
        $this->client = $client;
    }
    
    public function pull($entities)
    {
        $entities = $this->prepareEntities($entities);
        
        foreach ($entities as $entity) {
            if (!method_exists($entity, 'setRedisFields')) {
                throw new ManagerException(sprintf("Entity %s must have setRedisFields() method.", get_class($entity)));
            }
            
            $hashName = sprintf("%s:%d", end(explode("\\", get_class($entity))), $entity->getId());
            $fields = $this->client->hgetall($hashName);
            
            $entity->setRedisFields($fields);
        }
    }
    
    public function save($entities)
    {
        $entities = $this->prepareEntities($entities);
        
        foreach ($entities as $entity) {
            if (!method_exists($entity, 'getRedisFields')) {
                throw new ManagerException(sprintf("Entity %s must have getRedisFields() method.", get_class($entity)));
            }
            
            $hashName = sprintf("%s:%d", end(explode("\\", get_class($entity))), $entity->getId());
            $fields = $entity->getRedisFields();
            
            $this->client->hmset($hashName, $fields);
        }
    }
    
    private function prepareEntities($entities)
    {
        if (!is_array($entities)) {
            $entities = array($entities);
        }
        if (null === $entities) {
            throw new ManagerException("You should pass valid data.");
        }
        
        return $entities;
    }
}