<?php

namespace FOS\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use FOS\UserBundle\Model\User;

/*
 * This file is part of the FOS\UserBundle
 *
 * (c) Matthieu Bontemps <matthieu@knplabs.com>
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 * (c) Luis Cordova <cordoval@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * CreateUserCommand.
 *
 * @package    Bundle
 * @subpackage FOS\UserBundle
 * @author     Matthieu Bontemps <matthieu@knplabs.com>
 * @author     Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class PromoteSuperAdminCommand extends BaseCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('fos:user:promote')
            ->setDescription('Promotes a user as a super administrator')
            ->setDefinition(array(
                new InputArgument('username', InputArgument::REQUIRED, 'The username'),
            ))
            ->setHelp(<<<EOT
The <info>fos:user:promote</info> command promotes a user as a super administrator

  <info>php app/console fos:user:promote matthieu</info>
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

        $promoter = $this->container->get('fos_user.user_promoter');

        $promoter->promote($username);

        $output->writeln(sprintf('User "%s" has been promoted as a super administrator.', $username));
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
