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

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use FOS\UserBundle\Model\User;

/**
 * @author Mathias Verraes <mathias@verraes.net>
*/
class ListUsersCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('fos:user:list')
            ->setDescription('Lists users')
            ->setHelp(<<<EOT
The <info>fos:user:list</info> command shows a list of all users in the system, with their roles.
EOT
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userManager = $this->getContainer()->get('fos_user.user_manager');

        foreach($userManager->findUsers() as $user) {
            $output->writeln(sprintf('%s Roles: %s', 
                str_pad($user->getUserName(), 30),
                implode(', ', $user->getRoles())
            ));
        }
    }
}
