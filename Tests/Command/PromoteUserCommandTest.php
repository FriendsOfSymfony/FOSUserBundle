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

use FOS\UserBundle\Command\PromoteUserCommand;
use FOS\UserBundle\Util\UserManipulator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class PromoteUserCommandTest extends TestCase
{
    public function testExecute()
    {
        $commandTester = $this->createCommandTester($this->getManipulator('user', 'role', false));
        $exitCode = $commandTester->execute([
            'username' => 'user',
            'role' => 'role',
        ], [
            'decorated' => false,
            'interactive' => false,
        ]);

        $this->assertSame(0, $exitCode, 'Returns 0 in case of success');
        $this->assertRegExp('/Role "role" has been added to user "user"/', $commandTester->getDisplay());
    }

    public function testExecuteInteractiveWithQuestionHelper()
    {
        $application = new Application();

        $helper = $this->getMockBuilder('Symfony\Component\Console\Helper\QuestionHelper')
            ->setMethods(['ask'])
            ->getMock();

        $helper->expects($this->at(0))
            ->method('ask')
            ->will($this->returnValue('user'));
        $helper->expects($this->at(1))
            ->method('ask')
            ->will($this->returnValue('role'));

        $application->getHelperSet()->set($helper, 'question');

        $commandTester = $this->createCommandTester($this->getManipulator('user', 'role', false), $application);
        $exitCode = $commandTester->execute([], [
            'decorated' => false,
            'interactive' => true,
        ]);

        $this->assertSame(0, $exitCode, 'Returns 0 in case of success');
        $this->assertRegExp('/Role "role" has been added to user "user"/', $commandTester->getDisplay());
    }

    /**
     * @return CommandTester
     */
    private function createCommandTester(UserManipulator $manipulator, Application $application = null)
    {
        if (null === $application) {
            $application = new Application();
        }

        $application->setAutoExit(false);

        $command = new PromoteUserCommand($manipulator);

        $application->add($command);

        return new CommandTester($application->find('fos:user:promote'));
    }

    /**
     * @param $username
     * @param $role
     * @param $super
     *
     * @return mixed
     */
    private function getManipulator($username, $role, $super)
    {
        $manipulator = $this->getMockBuilder('FOS\UserBundle\Util\UserManipulator')
            ->disableOriginalConstructor()
            ->getMock();

        if ($super) {
            $manipulator
                ->expects($this->once())
                ->method('promote')
                ->with($username)
                ->will($this->returnValue(true))
            ;
        } else {
            $manipulator
                ->expects($this->once())
                ->method('addRole')
                ->with($username, $role)
                ->will($this->returnValue(true))
            ;
        }

        return $manipulator;
    }
}
