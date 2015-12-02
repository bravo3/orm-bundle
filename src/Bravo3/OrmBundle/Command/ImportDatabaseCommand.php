<?php
namespace Bravo3\OrmBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Import the entire database from a file
 */
class ImportDatabaseCommand extends ImportExportCommand
{
    protected function configure()
    {
        $this->setName('orm:import')
             ->setDescription('Import database to a file')
             ->addOption('list', 'l', InputOption::VALUE_REQUIRED, 'Path to entity list file', self::ENTITY_LIST)
             ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'Input format [tar|zip]', 'tar')
             ->addOption('input', 'i', InputOption::VALUE_REQUIRED, 'Input filename')
             ->addOption('batch', null, InputOption::VALUE_REQUIRED, 'Transaction batch size', '100');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->port($input, $output, false);
    }
}
