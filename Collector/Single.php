<?php

namespace Dark\RedisListBundle\Collector;

class Single extends Collector
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
        $result = array();

        foreach ($elements as $element) {
            list($class, $id) = explode(';', $element);

            $result[] = $this->catchEntity($class, $id);
        }

        return $result;
    }

    private function catchEntity($class, $id)
    {
        $qb = $this->em->createQueryBuilder();

        $qb->select('e')
            ->from($class, 'e')
            ->where('e.id = :id')
            ->orderBy('e.id', 'DESC')
            ->setParameter('id', $id)
        ;

        $query = $qb
            ->getQuery()
            ->useResultCache(true, $this->time, md5(sprintf('%s;%s', $class, $id)))
        ;

        return $query->getSingleResult();
    }

}