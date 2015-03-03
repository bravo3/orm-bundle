<?php
namespace Bravo3\OrmBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class OrmCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('orm.em')) {
            return;
        }

        $definition  = $container->getDefinition('orm.em');
        $subscribers = $container->findTaggedServiceIds('orm.event_subscriber');

        foreach ($subscribers as $id => $tags) {
            $definition->addMethodCall('addSubscriber', [new Reference($id)]);
        }
    }
}