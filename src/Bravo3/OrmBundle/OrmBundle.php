<?php

namespace Bravo3\OrmBundle;

use Bravo3\OrmBundle\DependencyInjection\OrmCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class OrmBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new OrmCompilerPass());
    }
}
