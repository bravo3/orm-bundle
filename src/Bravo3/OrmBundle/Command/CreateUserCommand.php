<?php
namespace Bravo3\OrmBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * Create an ORM user
 */
class CreateUserCommand extends AbstractUserCommand
{

    protected function configure()
    {
        $this->setName('user:create')->setDescription('Create a new user');
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
        $helper = $this->getHelper('question');

        $username = $helper->ask($input, $output, new Question('Username: '));
        if (!$username) {
            $output->writeln("<error>Invalid username</error>");
            return;
        }

        $pw_question = new Question('Password: ');
        $pw_question->setHidden(true);

        $password = $helper->ask($input, $output, $pw_question);
        if (!$password) {
            $output->writeln("<error>Invalid password</error>");
            return;
        }

        $roles = $this->getRoles(null, $input, $output);

        $output->writeln('Username: <info>'.$username.'</info>');
        $output->writeln('Roles:    <info>'.implode(', ', $roles).'</info>');
        $this->showRoleWarnings($roles, $output);

        if (!$helper->ask($input, $output, new ConfirmationQuestion('Create user? [y/N]: ', false))) {
            return;
        }

        $provider = $this->getContainer()->get('orm.user_provider');
        $provider->createUser($username, $password, $roles);
        $output->writeln("Created new user");
    }
}
