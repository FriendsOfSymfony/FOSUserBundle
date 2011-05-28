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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Acl\Exception\AclAlreadyExistsException;

/**
 * This command installs global access control entries (ACEs)
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 * @author Luis Cordova <cordoval@gmail.com>
 */
class InstallAcesCommand extends Command
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('fos:user:installAces')
            ->setDescription('Installs global ACEs')
            ->setHelp(<<<EOT
This command should be run once during the installation process of the entire bundle.
EOT
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $aceManager = $this->container->get('fos_user.ace_manager');
        $userClass = $this->container->get('fos_user.user_manager')->getClass();

        if (!$aceManager->hasAclProvider()) {
            $output->writeln('You must setup the ACL system, see the Symfony2 documentation for how to do this.');
            return;
        }

        try {
            $aceManager->installAces($userClass);
        } catch (AclAlreadyExistsException $exists) {
            $output->writeln('You already installed the global ACEs.');
            return;
        }

        $output->writeln('Global ACEs have been installed.');
    }
}
