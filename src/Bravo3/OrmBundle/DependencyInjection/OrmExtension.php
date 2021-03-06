<?php

namespace Bravo3\OrmBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\HttpKernel\Kernel;

class OrmExtension extends Extension
{
    const ORM_FACT_CLASS  = 'Bravo3\OrmBundle\Services\Factories\OrmFactory';
    const ORM_FACT_METHOD = 'createEntityManager';

    /**
     * @var callable
     */
    protected $autoloader;

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $em = $container->getDefinition('orm.em');
        $em->setFactory([self::ORM_FACT_CLASS, self::ORM_FACT_METHOD]);

        $container->getDefinition('orm.driver')
                  ->addArgument($config['params'])
                  ->addArgument($config['options'])
                  ->addArgument($config['sentinels']);

        $container->getDefinition('orm.user_provider')
                  ->addArgument($config['user_class'])
                  ->addArgument($config['user_roles']);

        $container->getDefinition('orm.security_manager')
                  ->addArgument($config['auth_firewall']);

        $container->getDefinition('orm.session_handler')
                  ->addArgument($config['sessions']['entity'])
                  ->addArgument($config['sessions']['ttl']);
    }
}
