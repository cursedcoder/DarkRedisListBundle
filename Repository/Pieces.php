<?php

namespace Dark\RedisListBundle\Repository;

class Pieces implements Repository
{
    private $em;
    private $time;

    public function __construct($em, $time)
    {
        $this->em = $em;
        $this->time = $time;
    }

    public function process($elements)
    {
        $pieces = array();
        $result = array();
        $piece = 0;

        foreach ($elements as $element) {
            if (empty($element)) {
                continue;
            }

            list($class, $id) = explode(';', $element);

            if(isset($pieces[$piece]) && key($pieces[$piece]) != $class) {
                $piece++;
            }

            $pieces[$piece][$class][] = $id;
        }

        foreach ($pieces as $piece) {
            $class = key($piece);
            $ids = array_values($piece[$class]);

            $result = array_merge($result, $this->catchEntity($class, $ids));
        }

        return $result;
    }

    private function catchEntity($class, $id)
    {
        if (!is_array($id)) {
            $id = array($id);
        }

        $ids = implode(',', $id);
        $qb = $this->em->createQueryBuilder();

        $qb->select('e')
           ->from($class, 'e')
           ->where(sprintf('e.id in (%s)', $ids))
        ;

        $query = $qb
            ->getQuery()
            ->useResultCache(true, $this->time, md5(sprintf('%s;%s', $class, implode(';', $id))))
        ;

        return $query->getResult();
    }
}