<?php
namespace Bravo3\OrmBundle\Command;

use Bravo3\Orm\Mappers\Portation\MapWriterInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Export metadata for entities to an auxiliary resource
 */
class ExportMapCommand extends AbstractEntityCommand
{
    protected function configure()
    {
        $this->setName('orm:map:export')
             ->setDescription('Export entity mappings')
             ->addOption('list', 'l', InputOption::VALUE_REQUIRED, 'Path to entity list file', self::ENTITY_LIST)
             ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'Output format', 'yaml');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entities   = $this->getEntities($input->getOption('list'));
        $map_writer = $this->getMapperForFormat($input->getOption('format'));

        foreach ($entities as $class_name) {
            $output->writeln("Compiling <info>".$class_name."</info>..");
            $map_writer->compileMetadataForEntity($class_name);
        }

        $output->write("Flushing.. ");
        $map_writer->flush();
        $output->writeln("<comment>COMPLETE</comment>");
    }

    /**
     * Get the map writer for the given format
     *
     * @param string $format
     * @return MapWriterInterface
     */
    protected function getMapperForFormat($format)
    {
        switch ($format) {
            default:
                throw new \InvalidArgumentException("Format '".$format."' is not supported");
            case 'yaml':
                return $this->getContainer()->get('orm.map_writer.yaml');
        }
    }
}
