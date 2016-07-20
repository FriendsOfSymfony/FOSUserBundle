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
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PromoteUserCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $commandTester = $this->createCommandTester($this->getContainer('user', 'role', false));
        $exitCode = $commandTester->execute(array(
            'command' => 'fos:user:promote', // BC for SF <2.4 see https://github.com/symfony/symfony/pull/8626
            'username' => 'user',
            'role' => 'role',
        ), array(
            'decorated' => false,
            'interactive' => false,
        ));

        $this->assertEquals(0, $exitCode, 'Returns 0 in case of success');
        $this->assertRegExp('/Role "role" has been added to user "user"/', $commandTester->getDisplay());
    }

    /**
     * @group legacy
     */
    public function testExecuteInteractiveWithDialogHelper()
    {
        if (!class_exists('Symfony\Component\Console\Helper\DialogHelper')) {
            $this->markTestSkipped('Using the DialogHelper is not possible on Symfony 3+.');
        }

        $application = new Application();

        $dialog = $this->getMock('Symfony\Component\Console\Helper\DialogHelper', array(
            'askAndValidate',
        ));
        $dialog->expects($this->at(0))
            ->method('askAndValidate')
            ->will($this->returnValue('user'));
        $dialog->expects($this->at(1))
            ->method('askAndValidate')
            ->will($this->returnValue('role'));

        $helperSet = new HelperSet(array(
            'dialog' => $dialog,
        ));
        $application->setHelperSet($helperSet);

        $commandTester = $this->createCommandTester($this->getContainer('user', 'role', false), $application);
        $exitCode = $commandTester->execute(array(
            'command' => 'fos:user:promote', // BC for SF <2.4 see https://github.com/symfony/symfony/pull/8626
        ), array(
            'decorated' => false,
            'interactive' => true,
        ));

        $this->assertEquals(0, $exitCode, 'Returns 0 in case of success');
        $this->assertRegExp('/Role "role" has been added to user "user"/', $commandTester->getDisplay());
    }

    public function testExecuteInteractiveWithQuestionHelper()
    {
        if (!class_exists('Symfony\Component\Console\Helper\QuestionHelper')) {
            $this->markTestSkipped('The question helper not available.');
        }

        $application = new Application();

        $helper = $this->getMock('Symfony\Component\Console\Helper\QuestionHelper', array(
            'ask',
        ));
        $helper->expects($this->at(0))
            ->method('ask')
            ->will($this->returnValue('user'));
        $helper->expects($this->at(1))
            ->method('ask')
            ->will($this->returnValue('role'));

        $application->getHelperSet()->set($helper, 'question');

        $commandTester = $this->createCommandTester($this->getContainer('user', 'role', false), $application);
        $exitCode = $commandTester->execute(array(), array(
            'decorated' => false,
            'interactive' => true,
        ));

        $this->assertEquals(0, $exitCode, 'Returns 0 in case of success');
        $this->assertRegExp('/Role "role" has been added to user "user"/', $commandTester->getDisplay());
    }

    private function createCommandTester(ContainerInterface $container, Application $application = null)
    {
        if (null === $application) {
            $application = new Application();
        }

        $application->setAutoExit(false);

        $command = new PromoteUserCommand();
        $command->setContainer($container);

        $application->add($command);

        return new CommandTester($application->find('fos:user:promote'));
    }

    private function getContainer($username, $role, $super)
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');

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

        $container
            ->expects($this->once())
            ->method('get')
            ->with('fos_user.util.user_manipulator')
            ->will($this->returnValue($manipulator));

        return $container;
    }
}
