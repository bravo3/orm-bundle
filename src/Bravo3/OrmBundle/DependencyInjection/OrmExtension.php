<?php

namespace Bravo3\OrmBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class OrmExtension extends Extension
{
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

        $container->getDefinition('orm.driver')
                  ->addArgument($config['params']);

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
