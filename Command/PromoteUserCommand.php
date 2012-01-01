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

use Symfony\Component\Console\Output\OutputInterface;
use FOS\UserBundle\Util\UserManipulator;

/**
 * @author Matthieu Bontemps <matthieu@knplabs.com>
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Luis Cordova <cordoval@gmail.com>
 * @author Lenar Lõhmus <lenar@city.ee>
 */
class PromoteUserCommand extends RoleCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('fos:user:promote')
            ->setDescription('Promotes a user by adding a role')
            ->setHelp(<<<EOT
The <info>fos:user:promote</info> command promotes a user by adding a role

  <info>php app/console fos:user:promote matthieu ROLE_CUSTOM</info>
  <info>php app/console fos:user:promote --super matthieu</info>
EOT
            );
    }

    protected function executeRoleCommand(UserManipulator $manipulator, OutputInterface $output, $username, $super, $role)
    {
        if ($super) {
            $manipulator->promote($username);
            $output->writeln(sprintf('User "%s" has been promoted as a super administrator.', $username));
        } else {
            if ($manipulator->addRole($username, $role)) {
                $output->writeln(sprintf('Role "%s" has been added to user "%s".', $role, $username));
            } else {
                $output->writeln(sprintf('User "%s" did already have "%s" role.', $username, $role));
            }
        }
    }
}
