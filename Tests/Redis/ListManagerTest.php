<?php

namespace Dark\RedisListBundle\Tests\Redis;

use Dark\RedisListBundle\Redis\ListManager;

class ListManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldRepairSuccesfully()
    {
        $fixture = array(1, 3, 5, 7, 9, 20);

        $mockedClient = $this->getMockedClient();

        $mockedClient->expects($this->once())
            ->method('hkeys')
            ->with($this->equalTo('sampleList'))
            ->will($this->returnValue($fixture));

        $mockedClient->expects($this->atLeastOnce())
            ->method('hlen')
            ->with($this->equalTo('sampleList'))
            ->will($this->returnValue(count($fixture)));

        $manager = new ListManager($mockedClient);
        $repair = $manager->repair('sampleList');
    }

    /**
     * @test
     * @expectedException Dark\RedisListBundle\Redis\ListException
     */
    public function shouldNotRepairWithBadData()
    {
        $fixture = array();

        $mockedClient = $this->getMockedClient();

        $mockedClient->expects($this->once())
            ->method('hkeys')
            ->with($this->equalTo('sampleList'))
            ->will($this->returnValue($fixture));

        $manager = new ListManager($mockedClient);
        $repair = $manager->repair('sampleList');
    }

    protected function getMockedClient()
    {
        $client = $this->getMock('Predis\Client', array('hlen', 'hget', 'hset', 'hdel', 'hgetall', 'hkeys'));

        return $client;
    }
}