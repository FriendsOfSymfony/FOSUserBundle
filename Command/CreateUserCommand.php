<?php

namespace Bundle\FOS\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

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
 * CreateUserCommand.
 *
 * @package    Bundle
 * @subpackage FOS\UserBundle
 * @author     Matthieu Bontemps <matthieu@knplabs.com>
 * @author     Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class CreateUserCommand extends BaseCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('fos:user:create')
            ->setDescription('Create a user.')
            ->setDefinition(array(
                new InputArgument('username', InputArgument::REQUIRED, 'The username'),
                new InputArgument('email', InputArgument::REQUIRED, 'The email'),
                new InputArgument('password', InputArgument::REQUIRED, 'The password'),
                new InputOption('super-admin', null, InputOption::VALUE_NONE, 'Set the user as super admin'),
                new InputOption('inactive', null, InputOption::VALUE_NONE, 'Set the user as inactive'),
            ))
            ->setHelp(<<<EOT
The <info>fos:user:create</info> command creates a user:

  <info>php app/console fos:user:create matthieu</info>

This interactive shell will first ask you for a password.

You can alternatively specify the password as a second argument:

  <info>php app/console fos:user:create matthieu mypassword</info>

You can create a super admin via the super-admin flag:

  <info>php app/console fos:user:create admin --super-admin</info>

You can create an inactive user (will not be able to log in):

  <info>php app/console fos:user:create thibault --inactive</info>

EOT
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userRepo = $this->container->get('fos_user.repository.user');
        $encoder = $this->container->get('fos_user.encoder');

        $user = $userRepo->createObjectInstance();
        $user->setUsername($input->getArgument('username'));
        $user->setEmail($input->getArgument('email'));
        $user->setAlgorithm($this->container->getParameter('fos_user.encoder.algorithm'));
        $user->setPassword($encoder->encodePassword($input->getArgument('password'), $user->getSalt()));
        $user->setEnabled(!$input->getOption('inactive'));
        $user->setSuperAdmin(!!$input->getOption('super-admin'));

        $userRepo->getObjectManager()->persist($user);
        $userRepo->getObjectManager()->flush();

        $output->writeln(sprintf('Created user <comment>%s</comment>', $user->getUsername()));
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

        if (!$input->getArgument('email')) {
            $email = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose an email:',
                function($email)
                {
                    if (empty($email)) {
                        throw new \Exception('Email can not be empty');
                    }
                    return $email;
                }
            );
            $input->setArgument('email', $email);
        }

        if (!$input->getArgument('password')) {
            $password = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a password:',
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
