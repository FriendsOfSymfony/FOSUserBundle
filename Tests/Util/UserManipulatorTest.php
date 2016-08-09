<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Tests\Util;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Util\UserManipulator;
use FOS\UserBundle\Tests\TestUser;

class UserManipulatorTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $userManagerMock = $this->getMock('FOS\UserBundle\Model\UserManagerInterface');
        $user = new TestUser();

        $username = 'test_username';
        $password = 'test_password';
        $email = 'test@email.org';
        $active = true; // it is enabled
        $superadmin = false;

        $userManagerMock->expects($this->once())
            ->method('createUser')
            ->will($this->returnValue($user));

        $userManagerMock->expects($this->once())
            ->method('updateUser')
            ->will($this->returnValue($user))
            ->with($this->isInstanceOf('FOS\UserBundle\Tests\TestUser'));

        $eventDispatcherMock = $this->getEventDispatcherMock(FOSUserEvents::USER_CREATED, true);

        $containerMock = $this->getContainerMock(true);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $containerMock);
        $manipulator->create($username, $password, $email, $active, $superadmin);

        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals($password, $user->getPlainPassword());
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($active, $user->isEnabled());
        $this->assertEquals($superadmin, $user->isSuperAdmin());
    }

    public function testActivateWithValidUsername()
    {
        $userManagerMock = $this->getMock('FOS\UserBundle\Model\UserManagerInterface');
        $username = 'test_username';

        $user = new TestUser();
        $user->setUsername($username);
        $user->setEnabled(false);

        $userManagerMock->expects($this->once())
            ->method('findUserByUsername')
            ->will($this->returnValue($user))
            ->with($this->equalTo($username));

        $userManagerMock->expects($this->once())
            ->method('updateUser')
            ->will($this->returnValue($user))
            ->with($this->isInstanceOf('FOS\UserBundle\Tests\TestUser'));

        $eventDispatcherMock = $this->getEventDispatcherMock(FOSUserEvents::USER_ACTIVATED, true);

        $containerMock = $this->getContainerMock(true);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $containerMock);
        $manipulator->activate($username);

        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals(true, $user->isEnabled());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testActivateWithInvalidUsername()
    {
        $userManagerMock = $this->getMock('FOS\UserBundle\Model\UserManagerInterface');
        $invalidusername = 'invalid_username';

        $userManagerMock->expects($this->once())
            ->method('findUserByUsername')
            ->will($this->returnValue(null))
            ->with($this->equalTo($invalidusername));

        $userManagerMock->expects($this->never())
            ->method('updateUser');

        $eventDispatcherMock = $this->getEventDispatcherMock(FOSUserEvents::USER_ACTIVATED, false);

        $containerMock = $this->getContainerMock(false);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $containerMock);
        $manipulator->activate($invalidusername);
    }

    public function testDeactivateWithValidUsername()
    {
        $userManagerMock = $this->getMock('FOS\UserBundle\Model\UserManagerInterface');
        $username = 'test_username';

        $user = new TestUser();
        $user->setUsername($username);
        $user->setEnabled(true);

        $userManagerMock->expects($this->once())
            ->method('findUserByUsername')
            ->will($this->returnValue($user))
            ->with($this->equalTo($username));

        $userManagerMock->expects($this->once())
            ->method('updateUser')
            ->will($this->returnValue($user))
            ->with($this->isInstanceOf('FOS\UserBundle\Tests\TestUser'));

        $eventDispatcherMock = $this->getEventDispatcherMock(FOSUserEvents::USER_DEACTIVATED, true);

        $containerMock = $this->getContainerMock(true);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $containerMock);
        $manipulator->deactivate($username);

        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals(false, $user->isEnabled());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDeactivateWithInvalidUsername()
    {
        $userManagerMock = $this->getMock('FOS\UserBundle\Model\UserManagerInterface');
        $invalidusername = 'invalid_username';

        $userManagerMock->expects($this->once())
            ->method('findUserByUsername')
            ->will($this->returnValue(null))
            ->with($this->equalTo($invalidusername));

        $userManagerMock->expects($this->never())
            ->method('updateUser');

        $eventDispatcherMock = $this->getEventDispatcherMock(FOSUserEvents::USER_DEACTIVATED, false);

        $containerMock = $this->getContainerMock(false);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $containerMock);
        $manipulator->deactivate($invalidusername);
    }

    public function testPromoteWithValidUsername()
    {
        $userManagerMock = $this->getMock('FOS\UserBundle\Model\UserManagerInterface');
        $username = 'test_username';

        $user = new TestUser();
        $user->setUsername($username);
        $user->setSuperAdmin(false);

        $userManagerMock->expects($this->once())
            ->method('findUserByUsername')
            ->will($this->returnValue($user))
            ->with($this->equalTo($username));

        $userManagerMock->expects($this->once())
            ->method('updateUser')
            ->will($this->returnValue($user))
            ->with($this->isInstanceOf('FOS\UserBundle\Tests\TestUser'));

        $eventDispatcherMock = $this->getEventDispatcherMock(FOSUserEvents::USER_PROMOTED, true);

        $containerMock = $this->getContainerMock(true);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $containerMock);
        $manipulator->promote($username);

        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals(true, $user->isSuperAdmin());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testPromoteWithInvalidUsername()
    {
        $userManagerMock = $this->getMock('FOS\UserBundle\Model\UserManagerInterface');
        $invalidusername = 'invalid_username';

        $userManagerMock->expects($this->once())
            ->method('findUserByUsername')
            ->will($this->returnValue(null))
            ->with($this->equalTo($invalidusername));

        $userManagerMock->expects($this->never())
            ->method('updateUser');

        $eventDispatcherMock = $this->getEventDispatcherMock(FOSUserEvents::USER_PROMOTED, false);

        $containerMock = $this->getContainerMock(false);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $containerMock);
        $manipulator->promote($invalidusername);
    }

    public function testDemoteWithValidUsername()
    {
        $userManagerMock = $this->getMock('FOS\UserBundle\Model\UserManagerInterface');
        $username    = 'test_username';

        $user = new TestUser();
        $user->setUsername($username);
        $user->setSuperAdmin(true);

        $userManagerMock->expects($this->once())
            ->method('findUserByUsername')
            ->will($this->returnValue($user))
            ->with($this->equalTo($username));

        $userManagerMock->expects($this->once())
            ->method('updateUser')
            ->will($this->returnValue($user))
            ->with($this->isInstanceOf('FOS\UserBundle\Tests\TestUser'));

        $eventDispatcherMock = $this->getEventDispatcherMock(FOSUserEvents::USER_DEMOTED, true);

        $containerMock = $this->getContainerMock(true);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $containerMock);
        $manipulator->demote($username);

        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals(false, $user->isSuperAdmin());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDemoteWithInvalidUsername()
    {
        $userManagerMock = $this->getMock('FOS\UserBundle\Model\UserManagerInterface');
        $invalidusername    = 'invalid_username';

        $userManagerMock->expects($this->once())
            ->method('findUserByUsername')
            ->will($this->returnValue(null))
            ->with($this->equalTo($invalidusername));

        $userManagerMock->expects($this->never())
            ->method('updateUser');

        $eventDispatcherMock = $this->getEventDispatcherMock(FOSUserEvents::USER_DEMOTED, false);

        $containerMock = $this->getContainerMock(false);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $containerMock);
        $manipulator->demote($invalidusername);
    }

    public function testChangePasswordWithValidUsername()
    {
        $userManagerMock = $this->getMock('FOS\UserBundle\Model\UserManagerInterface');

        $user = new TestUser();
        $username    = 'test_username';
        $password    = 'test_password';
        $oldpassword = 'old_password';

        $user->setUsername($username);
        $user->setPlainPassword($oldpassword);

        $userManagerMock->expects($this->once())
            ->method('findUserByUsername')
            ->will($this->returnValue($user))
            ->with($this->equalTo($username));

        $userManagerMock->expects($this->once())
            ->method('updateUser')
            ->will($this->returnValue($user))
            ->with($this->isInstanceOf('FOS\UserBundle\Tests\TestUser'));

        $eventDispatcherMock = $this->getEventDispatcherMock(FOSUserEvents::USER_PASSWORD_CHANGED, true);

        $containerMock = $this->getContainerMock(true);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $containerMock);
        $manipulator->changePassword($username, $password);

        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals($password, $user->getPlainPassword());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testChangePasswordWithInvalidUsername()
    {
        $userManagerMock = $this->getMock('FOS\UserBundle\Model\UserManagerInterface');

        $invalidusername  = 'invalid_username';
        $password         = 'test_password';

        $userManagerMock->expects($this->once())
            ->method('findUserByUsername')
            ->will($this->returnValue(null))
            ->with($this->equalTo($invalidusername));

        $userManagerMock->expects($this->never())
            ->method('updateUser');

        $eventDispatcherMock = $this->getEventDispatcherMock(FOSUserEvents::USER_PASSWORD_CHANGED, false);

        $containerMock = $this->getContainerMock(false);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $containerMock);
        $manipulator->changePassword($invalidusername, $password);
    }

    /**
     * @param string $event
     * @param bool   $once
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getEventDispatcherMock($event, $once = true)
    {
        $eventDispatcherMock = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $eventDispatcherMock->expects($once ? $this->once() : $this->never())
            ->method('dispatch')
            ->with($event);

        return $eventDispatcherMock;
    }

    /**
     * @param bool $once
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getContainerMock($once = true)
    {
        $containerMock = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');

        if (!class_exists('Symfony\Component\HttpFoundation\RequestStack')) {
            $containerMock->expects($this->any())
                ->method('has')
                ->willReturn(false)
                ->with('request_stack');

            $containerMock->expects($this->any())
                ->method('get')
                ->willReturn(null)
                ->with('request');

            $containerMock->expects($this->any())
                ->method('isScopeActive')
                ->willReturn(true)
                ->with('request');

            $containerMock->expects($this->any())
                ->method('isScopeActive');
        } else {
            $containerMock->expects($this->any())
                ->method('has')
                ->willReturn(true)
                ->with('request_stack');

            $requestStack = $this->getMock('Symfony\Component\HttpFoundation\RequestStack');

            $requestStack->expects($once ? $this->once() : $this->never())
                ->method('getCurrentRequest')
                ->willReturn(null);

            $containerMock->expects($this->any())
                ->method('get')
                ->willReturn($requestStack)
                ->with('request_stack');
        }

        return $containerMock;
    }
}
