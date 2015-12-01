<?php
namespace Bravo3\OrmBundle\Command;

use Bravo3\Orm\Mappers\Portation\MapWriterInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Rebuild an ORM table
 */
class ExportMapCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName('orm:map:export')
             ->setDescription('Export entity mappings')
             ->addArgument('directory', InputArgument::REQUIRED, 'Directory to scan for entities')
             ->addArgument('namespace', InputArgument::REQUIRED, 'Base namespace matching input directory')
             ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'Output format', 'yaml');
    }

    /**
     * Command executor
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $locator    = $this->getContainer()->get('orm.entity_locator');
        $entities   = $locator->locateEntities($input->getArgument('directory'), $input->getArgument('namespace'));
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
