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

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Antoine Hérault <antoine.herault@gmail.com>
 */
class DeactivateUserCommand extends HelperAwareCommand
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
        $username = $input->getArgument('username');

        $manipulator = $this->getContainer()->get('fos_user.util.user_manipulator');
        $manipulator->deactivate($username);

        $output->writeln(sprintf('User "%s" has been deactivated.', $username));
    }

    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('username')) {
            $username = $this->ask($input, $output, 'Please choose a username:', 'Username can not be empty');
            $input->setArgument('username', $username);
        }
    }
}
