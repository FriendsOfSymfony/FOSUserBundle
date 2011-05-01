<?php

namespace FOS\UserBundle\Tests\Command;

use FOS\UserBundle\Test\WebTestCase;
use FOS\UserBundle\Model\User;
use FOS\UserBundle\Command\CreateUserCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Tester\ApplicationTester;

class CreateUserCommandTest extends WebTestCase
{
    public function testUserCreation()
    {
        $kernel = $this->createKernel();
        $command = new CreateUserCommand();
        $application = new Application($kernel);
        $application->setAutoExit(false);
        $tester = new ApplicationTester($application);
        $username = 'test_username';
        $password = 'test_password';
        $email    = 'test_email@email.org';
        $tester->run(array(
            'command'  => $command->getFullName(),
            'username' => $username,
            'password' => $password,
            'email'    => $email,
        ), array('interactive' => false, 'decorated' => false, 'verbosity' => Output::VERBOSITY_VERBOSE));

        $userManager = $this->getService('fos_user.user_manager');
        $user = $userManager->findUserByUsername($username);

        $this->assertTrue($user instanceof User);
        $this->assertEquals($email, $user->getEmail());

        $userManager->deleteUser($user);
    }

    public function testUserCreationWithOptions()
    {
        $kernel = $this->createKernel();
        $command = new CreateUserCommand();
        $application = new Application($kernel);
        $application->setAutoExit(false);
        $tester = new ApplicationTester($application);
        $username = 'test_username';
        $password = 'test_password';
        $email    = 'test_email@email.org';
        $tester->run(array(
            'command'  => $command->getFullName(),
            'username' => $username,
            'password' => $password,
            'email'    => $email,
            '--active' => true,
            '--super-admin' => true
        ), array('interactive' => false, 'decorated' => false, 'verbosity' => Output::VERBOSITY_VERBOSE));

        $userManager = $this->getService('fos_user.user_manager');
        $user        = $userManager->findUserByUsername($username);

        $this->assertTrue($user instanceof User);
        $this->assertEquals($email, $user->getEmail());
        $this->assertFalse($user->isEnabled());
        $this->assertTrue($user->hasRole('ROLE_SUPERADMIN'));

        $userManager->deleteUser($user);
    }

    protected function tearDown()
    {
        $this->removeTestUser();
    }
}
