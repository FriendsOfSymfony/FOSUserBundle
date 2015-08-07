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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Matthieu Bontemps <matthieu@knplabs.com>
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Luis Cordova <cordoval@gmail.com>
 */
class CreateUserCommand extends ContainerAwareCommand
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
                new InputOption('property', null, InputOption::VALUE_OPTIONAL|InputOption::VALUE_IS_ARRAY, 'Set user property value'),
            ))
            ->setHelp(<<<EOT
The <info>fos:user:create</info> command creates a user:

  <info>php app/console fos:user:create matthieu</info>

This interactive shell will ask you for an email and then a password.

You can alternatively specify the email and password as the second and third arguments:

  <info>php app/console fos:user:create matthieu matthieu@example.com mypassword</info>

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
        $username   = $input->getArgument('username');
        $email      = $input->getArgument('email');
        $password   = $input->getArgument('password');
        $inactive   = $input->getOption('inactive');
        $superadmin = $input->getOption('super-admin');
        $properties = $input->getOption('property');

        $userproperties = $this->extractUserProperties($properties);

        $manipulator = $this->getContainer()->get('fos_user.util.user_manipulator');
        $manipulator->create($username, $password, $email, !$inactive, $superadmin, $userproperties);

        $output->writeln(sprintf('Created user <comment>%s</comment>', $username));
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
                function($username) {
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
                function($email) {
                    if (empty($email)) {
                        throw new \Exception('Email can not be empty');
                    }

                    return $email;
                }
            );
            $input->setArgument('email', $email);
        }

        if (!$input->getArgument('password')) {
            $password = $this->getHelper('dialog')->askHiddenResponseAndValidate(
                $output,
                'Please choose a password:',
                function($password) {
                    if (empty($password)) {
                        throw new \Exception('Password can not be empty');
                    }

                    return $password;
                }
            );
            $input->setArgument('password', $password);
        }
    }

    /**
     * Extracts the name, type and value from an array containing string with "name(type)=value" format. Type is string
     * if no type supplied.
     *
     * @param $properties
     *
     * @return array
     */
    private function extractUserProperties($properties)
    {
        $userproperties = array();
        foreach($properties as $property) {
            $parts = explode('=', $property, 2);
            if(!empty($parts)) {
                // Get the property type
                $type = 'string';
                $matches = array();
                preg_match('~\((.*?)\)~', $parts[0], $matches);
                if (!empty($matches) && 2 === count($matches)) {
                    $validTypes = array(
                        'null',
                        'bool',
                        'boolean',
                        'int',
                        'integer',
                        'float',
                    );
                    if (in_array($matches[1], $validTypes)) {
                        $type = $matches[1];
                    }
                }

                // Get the property name and translate it to a setter
                $method = null;
                $nameParts = explode('(', $parts[0], 2);
                if (!empty($nameParts)) {
                    $method = $nameParts[0];
                } else {
                    $method = $parts[0];
                }
                $method = 'set' . ucwords($method);

                // Get the property value
                $value = $parts[1];
                settype($value, $type);

                $userproperties[$method] = $value;
            }
        }

        return $userproperties;
    }
}
