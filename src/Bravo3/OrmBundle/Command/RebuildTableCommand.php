<?php
namespace Bravo3\OrmBundle\Command;

use Bravo3\Orm\Services\Maintenance;
use Bravo3\OrmBundle\Services\OutputLogger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Rebuild an ORM table
 */
class RebuildTableCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName('orm:rebuild')
             ->setDescription('Rebuild an ORM table')
             ->addArgument('class', InputArgument::REQUIRED, 'Entity class name');
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
        $maintenance = new Maintenance($this->getContainer()->get('orm.em'), new OutputLogger($output));
        $maintenance->rebuild(str_replace('/', '\\', $input->getArgument('class')));
    }
}
