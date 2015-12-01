<?php
namespace Bravo3\OrmBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Yaml\Yaml;

abstract class AbstractEntityCommand extends ContainerAwareCommand
{
    const ENTITY_LIST = 'app/config/entities.yml';

    /**
     * Parse an entity folder list YAML file and return a list of entities in all folders
     *
     * @param string $list_fn
     * @return string[]
     */
    protected function getEntities($list_fn)
    {
        if (!is_readable($list_fn)) {
            throw new \InvalidArgumentException("Input file is not readable");
        }

        $locator = $this->getContainer()->get('orm.entity_locator');
        $yaml    = new Yaml();
        $data    = $yaml->parse(file_get_contents($list_fn));
        $out     = [];

        foreach ($data as $entity_folder) {
            $out = array_merge($out, $locator->locateEntities($entity_folder['path'], $entity_folder['namespace']));
        }

        return $out;
    }
}
