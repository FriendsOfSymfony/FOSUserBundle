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

use FOS\UserBundle\Command\CreateUserCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CreateUserCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $commandTester = $this->createCommandTester($this->getContainer('user', 'pass', 'email', true, false));
        $exitCode = $commandTester->execute(array(
            'command' => 'fos:user:create', // BC for SF <2.4 see https://github.com/symfony/symfony/pull/8626
            'username' => 'user',
            'email' => 'email',
            'password' => 'pass',
        ), array(
            'decorated' => false,
            'interactive' => false,
        ));

        $this->assertEquals(0, $exitCode, 'Returns 0 in case of success');
        $this->assertRegExp('/Created user user/', $commandTester->getDisplay());
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
            'askHiddenResponseAndValidate',
        ));
        $dialog->expects($this->at(0))
            ->method('askAndValidate')
            ->will($this->returnValue('user'));

        $dialog->expects($this->at(1))
            ->method('askAndValidate')
            ->will($this->returnValue('email'));

        $dialog->expects($this->at(2))
            ->method('askHiddenResponseAndValidate')
            ->will($this->returnValue('pass'));

        $helperSet = new HelperSet(array(
            'dialog' => $dialog,
        ));
        $application->setHelperSet($helperSet);

        $commandTester = $this->createCommandTester(
            $this->getContainer('user', 'pass', 'email', true, false), $application
        );
        $exitCode = $commandTester->execute(array(
            'command' => 'fos:user:create', // BC for SF <2.4 see https://github.com/symfony/symfony/pull/8626
        ), array(
            'decorated' => false,
            'interactive' => true,
        ));

        $this->assertEquals(0, $exitCode, 'Returns 0 in case of success');
        $this->assertRegExp('/Created user user/', $commandTester->getDisplay());
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
            ->will($this->returnValue('email'));

        $helper->expects($this->at(2))
            ->method('ask')
            ->will($this->returnValue('pass'));

        $application->getHelperSet()->set($helper, 'question');

        $commandTester = $this->createCommandTester(
            $this->getContainer('user', 'pass', 'email', true, false), $application
        );
        $exitCode = $commandTester->execute(array(), array(
            'decorated' => false,
            'interactive' => true,
        ));

        $this->assertEquals(0, $exitCode, 'Returns 0 in case of success');
        $this->assertRegExp('/Created user user/', $commandTester->getDisplay());
    }

    private function createCommandTester(ContainerInterface $container, Application $application = null)
    {
        if (null === $application) {
            $application = new Application();
        }

        $application->setAutoExit(false);

        $command = new CreateUserCommand();
        $command->setContainer($container);

        $application->add($command);

        return new CommandTester($application->find('fos:user:create'));
    }

    private function getContainer($username, $password, $email, $active, $superadmin)
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');

        $manipulator = $this->getMockBuilder('FOS\UserBundle\Util\UserManipulator')
            ->disableOriginalConstructor()
            ->getMock();

        $manipulator
            ->expects($this->once())
            ->method('create')
            ->with($username, $password, $email, $active, $superadmin)
        ;

        $container
            ->expects($this->once())
            ->method('get')
            ->with('fos_user.util.user_manipulator')
            ->will($this->returnValue($manipulator));

        return $container;
    }
}
