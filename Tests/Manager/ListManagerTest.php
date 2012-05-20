<?php

namespace Dark\RedisListBundle\Tests\Redis;

use Dark\RedisListBundle\Manager\ListManager;

class ListManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException Dark\RedisListBundle\Manager\ManagerException
     */
    public function shouldNotRepairWithBadData()
    {
        $fixture = array();

        $mockedClient = $this->getMockedClient();

        $mockedClient->expects($this->once())
            ->method('hgetall')
            ->with($this->equalTo('sampleList'))
            ->will($this->returnValue($fixture));

        $manager = new ListManager($mockedClient);
        $repair = $manager->repair('sampleList');
    }

    protected function getMockedClient()
    {
        $client = $this->getMock('Predis\Client', array('del', 'hgetall', 'hmset'));

        return $client;
    }
}