<?php

namespace Bravo3\OrmBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\HttpKernel\Kernel;

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
                  ->addArgument((int)Kernel::MINOR_VERSION < 6 ? '@security_context' : '@security.token_storage')
                  ->addArgument($config['auth_firewall']);

        $container->getDefinition('orm.session_handler')
                  ->addArgument($config['sessions']['entity'])
                  ->addArgument($config['sessions']['ttl']);
    }
}
