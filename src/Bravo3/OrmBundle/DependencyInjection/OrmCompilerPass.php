<?php
namespace Bravo3\OrmBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class OrmCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // Sub-mappers for the chained mapper
        if ($container->hasDefinition('orm.mapper.chained')) {
            $definition  = $container->getDefinition('orm.mapper.chained');
            $subscribers = $container->findTaggedServiceIds('orm.mapper');

            foreach ($subscribers as $id => $tags) {
                $definition->addMethodCall('registerMapper', [new Reference($id)]);
            }
        }

        // Event subscribers on the entity manager
        $definition  = $container->getDefinition('orm.em');
        $subscribers = $container->findTaggedServiceIds('orm.event_subscriber');

        foreach ($subscribers as $id => $tags) {
            $definition->addMethodCall('addSubscriber', [new Reference($id)]);
        }
    }
}