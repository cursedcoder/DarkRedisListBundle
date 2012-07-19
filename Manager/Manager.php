<?php

namespace Dark\RedisListBundle\Manager;

use Predis\Client;

class Manager
{
    private $client;

    public function __construct(Client $client)
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
            
            $hashName = sprintf("%s:%d", end((explode("\\", get_class($entity)))), $entity->getId());

            $fields = $entity->getRedisFields();
            $fields = $this->client->hmget($hashName, $fields);

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
            
            $hashName = sprintf("%s:%d", end((explode("\\", get_class($entity)))), $entity->getId());
            $fields = $entity->getRedisFields();

            $this->client->hmset($hashName, $fields);
        }
    }

    public function remove($entities)
    {
        $entities = $this->prepareEntities($entities);

        foreach ($entities as $entity) {
            $hashName = sprintf("%s:%d", end((explode("\\", get_class($entity)))), $entity->getId());
            $this->client->del($hashName);
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

    private function processExpr(&$fields)
    {
        foreach ($fields as $i => $field) {
            if (is_array($field)) {


                unset($fields[$i]);
            }
        }
    }
}