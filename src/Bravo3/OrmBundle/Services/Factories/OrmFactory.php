<?php
namespace Bravo3\OrmBundle\Services\Factories;

use Bravo3\Orm\Config\Configuration;
use Bravo3\Orm\Drivers\DriverInterface;
use Bravo3\Orm\Mappers\Annotation\AnnotationMapper;
use Bravo3\Orm\Services\EntityManager;

class OrmFactory
{
    /**
     * Build an entity manager
     *
     * @param DriverInterface $driver
     * @param string          $cache_dir
     * @param bool            $hydration_exceptions_as_events
     * @return EntityManager
     */
    public static function createEntityManager(
        DriverInterface $driver,
        $cache_dir,
        $hydration_exceptions_as_events = false
    ) {
        $mapper = new AnnotationMapper();
        $config = new Configuration();
        $config->setCacheDir($cache_dir);
        $config->setHydrationExceptionsAsEvents($hydration_exceptions_as_events);
        return EntityManager::build($driver, $mapper, null, null, $config);
    }
}
