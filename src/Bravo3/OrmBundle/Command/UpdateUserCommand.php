<?php
namespace Bravo3\OrmBundle\Command;

use Bravo3\OrmBundle\Entity\User;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * Update an ORM users password and roles
 */
class UpdateUserCommand extends AbstractUserCommand
{

    protected function configure()
    {
        $this->setName('user:update')
             ->setDescription('Update a users credentials & roles')
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

        /** @var User $user */
        $user = $provider->loadUserByUsername($input->getArgument('username'));

        $pw_question = new Question('New password: ');
        $pw_question->setHidden(true);

        $password = $helper->ask($input, $output, $pw_question);
        if (!$password) {
            $output->writeln("<error>Invalid password</error>");
            return;
        }

        $roles = $this->getRoles($user->getRoles(), $input, $output);

        $output->writeln('Username: <info>'.$user->getUsername().'</info>');
        $output->writeln('Roles:    <info>'.implode(', ', $roles).'</info>');
        $this->showRoleWarnings($roles, $output);

        if (!$helper->ask($input, $output, new ConfirmationQuestion('Update user? [y/N]: ', false))) {
            return;
        }

        $user->setRoles($roles);

        $provider = $this->getContainer()->get('orm.user_provider');
        $provider->updateUserCredentials($user, $password);
        $output->writeln("User updated");
    }
}
