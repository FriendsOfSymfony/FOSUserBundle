<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Test;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Command\Command;

abstract class WebTestCase extends \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
{
    protected $kernel;

    protected function runCommand($name, array $params = array())
    {
        \array_unshift($params, $name);

        $kernel = $this->createKernel();
        $kernel->boot();

        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput($params);
        $input->setInteractive(false);

        $ouput = new NullOutput(0);

        $application->run($input, $ouput);
    }

    protected function getService($name)
    {
        if (null === $this->kernel) {
            $this->kernel = $this->createKernel();
            $this->kernel->boot();
        }

        return $this->kernel->getContainer()->get($name);
    }

    protected function removeTestUser()
    {
        $userManager = $this->getService('fos_user.user_manager');
        if ($user = $userManager->findUserByUsername('test_username')) {
            $userManager->deleteUser($user);
        }
    }
}
