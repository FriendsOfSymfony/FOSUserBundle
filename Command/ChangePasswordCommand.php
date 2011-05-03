<?php

namespace FOS\UserBundle\Command;

use FOS\UserBundle\Model\User;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Bundle\FrameworkBundle\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * CreateUserCommand
 */
class ChangePasswordCommand extends BaseCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('fos:user:changePassword')
            ->setDescription('Change the password of a user.')
            ->setDefinition(array(
                new InputArgument('username', InputArgument::REQUIRED, 'The username'),
                new InputArgument('password', InputArgument::REQUIRED, 'The password'),
            ))
            ->setHelp(<<<EOT
The <info>fos:user:changePassword</info> command changes the password of a user:

  <info>php app/console fos:user:changePassword matthieu</info>

This interactive shell will first ask you for a password.

You can alternatively specify the password as a second argument:

  <info>php app/console fos:user:changePassword matthieu mypassword</info>

EOT
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cliToken = new UsernamePasswordToken('command.line', null, $this->container->getParameter('fos_user.firewall_name'), array(User::ROLE_SUPERADMIN));
        $this->container->get('security.context')->setToken($cliToken);

        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        $changer = $this->container->get('fos_user.user_password_changer');

        $changer->change($username, $password);

        $output->writeln(sprintf('Changed password for user <comment>%s</comment>', $username));
    }

    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('username')) {
            $username = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please give the username:',
                function($username)
                {
                    if (empty($username)) {
                        throw new \Exception('Username can not be empty');
                    }
                    return $username;
                }
            );
            $input->setArgument('username', $username);
        }

        if (!$input->getArgument('password')) {
            $password = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please enter the new password:',
                function($password)
                {
                    if (empty($password)) {
                        throw new \Exception('Password can not be empty');
                    }
                    return $password;
                }
            );
            $input->setArgument('password', $password);
        }
    }
}
