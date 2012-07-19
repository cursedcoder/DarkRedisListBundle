<?php

namespace Dark\RedisListBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class DarkRedisListExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('dark_redis_list.template', $config['template']);
        $container->setParameter('dark_redis_list.time', $config['time']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if (true === $config['use_listener']) {
            $loader->load('listener.yml');
        }

        $collector = $config['collector'];

        if (!in_array($collector, array('single', 'pieces'))) {
            throw new \InvalidArgumentException('Bad collector name, you can use only "single" or "pieces"');
        }

        $container->setAlias('dark_redis_list.collector', 'dark_redis_list.collector.' . $collector);
    }
}
