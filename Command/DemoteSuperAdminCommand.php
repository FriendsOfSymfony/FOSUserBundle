<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use FOS\UserBundle\Model\User;

/**
 * @author Antoine Hérault <antoine.herault@gmail.com>
 */
class DemoteSuperAdminCommand extends Command
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('fos:user:demote')
            ->setDescription('Demote a super administrator as a simple user')
            ->setDefinition(array(
                new InputArgument('username', InputArgument::REQUIRED, 'The username'),
            ))
            ->setHelp(<<<EOT
The <info>fos:user:demote</info> command demotes a super administrator as a simple user

  <info>php app/console fos:user:demote matthieu</info>
EOT
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cliToken = new UsernamePasswordToken('command.line', null, $this->container->getParameter('fos_user.firewall_name'), array(User::ROLE_SUPER_ADMIN));
        $this->container->get('security.context')->setToken($cliToken);

        $username = $input->getArgument('username');

        $manipulator = $this->container->get('fos_user.util.user_manipulator');
        $manipulator->demote($username);

        $output->writeln(sprintf('Super administrator "%s" has been demoted as a simple user.', $username));
    }

    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('username')) {
            $username = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a username:',
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
    }
}
