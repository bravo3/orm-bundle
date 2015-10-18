<?php
namespace Bravo3\OrmBundle\Services\Factories;

use Bravo3\Orm\Config\Configuration;
use Bravo3\Orm\Drivers\DriverInterface;
use Bravo3\Orm\Mappers\MapperInterface;
use Bravo3\Orm\Services\EntityManager;

class OrmFactory
{
    /**
     * Build an entity manager
     *
     * @param DriverInterface $driver
     * @param MapperInterface $mapper
     * @param string          $cache_dir
     * @return EntityManager
     */
    public static function createEntityManager(
        DriverInterface $driver,
        MapperInterface $mapper,
        $cache_dir
    ) {
        $config = new Configuration();
        $config->setCacheDir($cache_dir);
        return EntityManager::build($driver, $mapper, null, null, $config);
    }
}
