<?php

namespace Bundle\FOS\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Security\Authentication\Token\UsernamePasswordToken;
use Bundle\FOS\UserBundle\Model\User;

/*
 * This file is part of the FOS\UserBundle
 *
 * (c) Matthieu Bontemps <matthieu@knplabs.com>
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * DeactivateUserCommand.
 *
 * @package    Bundle
 * @subpackage FOS\UserBundle
 * @author     Antoine Hérault <antoine.herault@gmail.com>
 */
class DeactivateUserCommand extends BaseCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('fos:user:deactivate')
            ->setDescription('Deactivate a user')
            ->setDefinition(array(
                new InputArgument('username', InputArgument::REQUIRED, 'The username'),
            ))
            ->setHelp(<<<EOT
The <info>fos:user:deactivate</info> command deactivates a user (will not be able to log in)

  <info>php app/console fos:user:deactivate matthieu</info>
EOT
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->container->get('security.context')->setToken(new UsernamePasswordToken('command.line', null, array(User::ROLE_SUPERADMIN)));

        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername($input->getArgument('username'));

        if (!$user) {
            throw new \InvalidArgumentException(sprintf('The user "%s" does not exist', $input->getArgument('username')));
        }
        $user->setEnabled(false);

        $userManager->updateUser($user);

        $output->writeln(sprintf('User "%s" has been deactivated.', $user->getUsername()));
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
