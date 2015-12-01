<?php
namespace Bravo3\OrmBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

abstract class AbstractUserCommand extends ContainerAwareCommand
{
    /**
     * Display a warning if obvious roles (eg ROLE_USER) are missing
     *
     * @param string[]        $roles
     * @param OutputInterface $output
     */
    protected function showRoleWarnings(array $roles, OutputInterface $output)
    {
        if (!in_array('ROLE_USER', $roles)) {
            $output->writeln('<error>WARNING:</error> <comment>ROLE_USER</comment> not in user roles');
        }
    }

    /**
     * Get a list of roles from the user
     *
     * @param string[]        $default
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return string[]
     */
    protected function getRoles(array $default = null, InputInterface $input, OutputInterface $output)
    {
        $roles_available = $this->getContainer()->get('orm.user_provider')->getAvailableRoles();
        $helper          = $this->getHelper('question');

        $default_indices = [];
        $roles           = [];

        foreach ($roles_available as $index => $role) {
            $roles[$index + 1] = $role;

            if (!$default) {
                $default = [$role];
            }

            if (in_array($role, $default)) {
                $default_indices[] = $index + 1;
            }
        }

        $default_roles = implode(',', $default_indices);

        $roles_question = new ChoiceQuestion(
            'User roles ['.$default_roles.']: ',
            $roles,
            $default_roles
        );

        $roles_question->setMultiselect(true);
        return $helper->ask($input, $output, $roles_question);
    }
}