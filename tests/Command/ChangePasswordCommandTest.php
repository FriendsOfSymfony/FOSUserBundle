<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Tests\Command;

use FOS\UserBundle\Command\ChangePasswordCommand;
use FOS\UserBundle\Util\UserManipulator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ChangePasswordCommandTest extends TestCase
{
    public function testExecute()
    {
        $commandTester = $this->createCommandTester($this->getManipulator('user', 'pass'));
        $exitCode = $commandTester->execute([
            'username' => 'user',
            'password' => 'pass',
        ], [
            'decorated' => false,
            'interactive' => false,
        ]);

        $this->assertSame(0, $exitCode, 'Returns 0 in case of success');
        $this->assertMatchesRegularExpression('/Changed password for user user/', $commandTester->getDisplay());
    }

    public function testExecuteInteractiveWithQuestionHelper()
    {
        $application = new Application();

        $helper = $this->getMockBuilder('Symfony\Component\Console\Helper\QuestionHelper')
            ->setMethods(['ask'])
            ->getMock();

        $helper->expects($this->exactly(2))
            ->method('ask')
            ->willReturnOnConsecutiveCalls('user', 'pass');

        $application->getHelperSet()->set($helper, 'question');

        $commandTester = $this->createCommandTester($this->getManipulator('user', 'pass'), $application);
        $exitCode = $commandTester->execute([], [
            'decorated' => false,
            'interactive' => true,
        ]);

        $this->assertSame(0, $exitCode, 'Returns 0 in case of success');
        $this->assertMatchesRegularExpression('/Changed password for user user/', $commandTester->getDisplay());
    }

    private function createCommandTester(UserManipulator $userManipulator, ?Application $application = null): CommandTester
    {
        if (null === $application) {
            $application = new Application();
        }

        $application->setAutoExit(false);

        $command = new ChangePasswordCommand($userManipulator);

        $application->addCommands([$command]);

        return new CommandTester($application->find('fos:user:change-password'));
    }

    private function getManipulator(string $username, string $password): UserManipulator
    {
        $manipulator = $this->getMockBuilder('FOS\UserBundle\Util\UserManipulator')
            ->disableOriginalConstructor()
            ->getMock();

        $manipulator
            ->expects($this->once())
            ->method('changePassword')
            ->with($username, $password)
        ;

        return $manipulator;
    }
}
