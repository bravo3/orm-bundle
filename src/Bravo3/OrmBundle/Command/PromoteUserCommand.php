<?php
namespace Bravo3\OrmBundle\Command;

use Bravo3\OrmBundle\Entity\User;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Change a users roles
 */
class PromoteUserCommand extends AbstractUserCommand
{

    protected function configure()
    {
        $this->setName('user:promote')
             ->setDescription('Change a users roles')
             ->addArgument('username', InputArgument::REQUIRED, 'Username');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper   = $this->getHelper('question');
        $provider = $this->getContainer()->get('orm.user_provider');

        /** @var User $user */
        $user = $provider->loadUserByUsername($input->getArgument('username'));

        $roles = $this->getRoles($user->getRoles(), $input, $output);

        $output->writeln('Username: <info>'.$user->getUsername().'</info>');
        $output->writeln('Roles:    <info>'.implode(', ', $roles).'</info>');
        $this->showRoleWarnings($roles, $output);

        if (!$helper->ask($input, $output, new ConfirmationQuestion('Update user? [y/N]: ', false))) {
            return;
        }

        $user->setRoles($roles);
        $em = $this->getContainer()->get('orm.em');
        $em->persist($user)->flush();

        $output->writeln("User updated");
    }
}
