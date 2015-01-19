<?php
namespace Bravo3\OrmBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Delete an ORM user
 */
class DeleteUserCommand extends AbstractUserCommand
{

    protected function configure()
    {
        $this->setName('user:delete')
             ->setDescription('Delete a user')
             ->addArgument('username', InputArgument::REQUIRED, 'Username');
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
        $helper   = $this->getHelper('question');
        $provider = $this->getContainer()->get('orm.user_provider');
        $user     = $provider->loadUserByUsername($input->getArgument('username'));

        $output->writeln(
            'WARNING: User "<info>'.$user->getUsername().'</info>" will be deleted. You can not undo this.'
        );

        if (!$helper->ask($input, $output, new ConfirmationQuestion('Confirm delete? [y/N]: ', false))) {
            return;
        }

        $em = $this->getContainer()->get('orm.em');
        $em->delete($user)->flush();

        $output->writeln("User deleted");
    }
}
