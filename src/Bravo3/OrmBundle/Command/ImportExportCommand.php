<?php
namespace Bravo3\OrmBundle\Command;

use Bravo3\Orm\Drivers\Filesystem\Enum\ArchiveType;
use Bravo3\Orm\Drivers\Filesystem\FilesystemDriver;
use Bravo3\Orm\Drivers\Filesystem\Io\PharIoDriver;
use Bravo3\Orm\Services\EntityManager;
use Bravo3\Orm\Services\Porter;
use Bravo3\OrmBundle\Services\OutputLogger;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class ImportExportCommand extends AbstractEntityCommand
{
    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param bool            $export
     * @return void
     */
    protected function port(InputInterface $input, OutputInterface $output, $export = true)
    {
        $entities = $this->getEntities($input->getOption('list'));
        $em       = $this->getContainer()->get('orm.em');
        $porter   = new Porter(new OutputLogger($output));
        $porter->registerManager('PRIMARY', $em);

        $io = new PharIoDriver(
            $input->getOption($export ? 'output' : 'input'),
            ArchiveType::memberByKey(strtoupper($input->getOption('format')))
        );

        $driver = new FilesystemDriver($io);
        $aux    = EntityManager::build($driver, $em->getMapper(), $em->getSerialiserMap());
        $porter->registerManager('AUX', $aux);

        $batch_size = max(1, min(1000, (int)$input->getOption('batch')));
        $term       = $export ? 'Exporting' : 'Importing';

        foreach ($entities as $class_name) {
            $output->writeln($term." <info>".$class_name."</info>..");
            try {
                if ($export) {
                    $porter->portTable($class_name, 'PRIMARY', 'AUX', $batch_size);
                } else {
                    $porter->portTable($class_name, 'AUX', 'PRIMARY', $batch_size);
                }
            } catch (\Exception $e) {
                $output->writeln("<error>ERROR:</error> ".$e->getMessage());
            }
        }

        if ($export) {
            $output->writeln("<comment>EXPORT COMPLETE</comment>");
        } else {
            $output->writeln("<comment>IMPORT COMPLETE</comment>");
        }
    }
}