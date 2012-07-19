<?php

namespace Dark\RedisListBundle\Doctrine;

trait RedisFields
{
    public function setRedisFields($data)
    {
        foreach ($data as $field => $value) {
            if (in_array($field, $this->redisFields)) {
                $this->$field = $value;
            }
        }
    }

    public function getRedisFields()
    {
        $fields = array();

        foreach ($this->redisFields as $field) {
            $fields[$field] = $this->$field;
        }

        return $fields;
    }
}
