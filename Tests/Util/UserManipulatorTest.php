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
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Tests\TestUser;
use FOS\UserBundle\Util\UserManipulator;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class UserManipulatorTest extends TestCase
{
    public function testCreate()
    {
        $userManagerMock = $this->getMockBuilder(UserManagerInterface::class)->getMock();
        $user = new TestUser();

        $username = 'test_username';
        $password = 'test_password';
        $email = 'test@email.org';
        $active = true; // it is enabled
        $superadmin = false;

        $userManagerMock->expects($this->once())
            ->method('createUser')
            ->willReturn($user);

        $userManagerMock->expects($this->once())
            ->method('updateUser')
            ->willReturn($user)
            ->with($this->isInstanceOf(TestUser::class));

        $eventDispatcherMock = $this->getEventDispatcherMock(FOSUserEvents::USER_CREATED, true);

        $requestStackMock = $this->getRequestStackMock(true);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->create($username, $password, $email, $active, $superadmin);

        $this->assertSame($username, $user->getUsername());
        $this->assertSame($password, $user->getPlainPassword());
        $this->assertSame($email, $user->getEmail());
        $this->assertSame($active, $user->isEnabled());
        $this->assertSame($superadmin, $user->isSuperAdmin());
    }

    public function testActivateWithValidUsername()
    {
        $userManagerMock = $this->getMockBuilder(UserManagerInterface::class)->getMock();
        $username = 'test_username';

        $user = new TestUser();
        $user->setUsername($username);
        $user->setEnabled(false);

        $userManagerMock->expects($this->once())
            ->method('findUserByUsername')
            ->willReturn($user)
            ->with($this->equalTo($username));

        $userManagerMock->expects($this->once())
            ->method('updateUser')
            ->willReturn($user)
            ->with($this->isInstanceOf(TestUser::class));

        $eventDispatcherMock = $this->getEventDispatcherMock(FOSUserEvents::USER_ACTIVATED, true);

        $requestStackMock = $this->getRequestStackMock(true);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->activate($username);

        $this->assertSame($username, $user->getUsername());
        $this->assertTrue($user->isEnabled());
    }

    public function testActivateWithInvalidUsername()
    {
        $this->expectException(\InvalidArgumentException::class);
        $userManagerMock = $this->getMockBuilder('FOS\UserBundle\Model\UserManagerInterface')->getMock();
        $invalidusername = 'invalid_username';

        $userManagerMock->expects($this->once())
            ->method('findUserByUsername')
            ->willReturn(null)
            ->with($this->equalTo($invalidusername));

        $userManagerMock->expects($this->never())
            ->method('updateUser');

        $eventDispatcherMock = $this->getEventDispatcherMock(FOSUserEvents::USER_ACTIVATED, false);

        $requestStackMock = $this->getRequestStackMock(false);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->activate($invalidusername);
    }

    public function testDeactivateWithValidUsername()
    {
        $userManagerMock = $this->getMockBuilder('FOS\UserBundle\Model\UserManagerInterface')->getMock();
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

        $requestStackMock = $this->getRequestStackMock(true);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->deactivate($username);

        $this->assertSame($username, $user->getUsername());
        $this->assertFalse($user->isEnabled());
    }

    public function testDeactivateWithInvalidUsername()
    {
        $this->expectException(\InvalidArgumentException::class);
        $userManagerMock = $this->getMockBuilder('FOS\UserBundle\Model\UserManagerInterface')->getMock();
        $invalidusername = 'invalid_username';

        $userManagerMock->expects($this->once())
            ->method('findUserByUsername')
            ->will($this->returnValue(null))
            ->with($this->equalTo($invalidusername));

        $userManagerMock->expects($this->never())
            ->method('updateUser');

        $eventDispatcherMock = $this->getEventDispatcherMock(FOSUserEvents::USER_DEACTIVATED, false);

        $requestStackMock = $this->getRequestStackMock(false);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->deactivate($invalidusername);
    }

    public function testPromoteWithValidUsername()
    {
        $userManagerMock = $this->getMockBuilder('FOS\UserBundle\Model\UserManagerInterface')->getMock();
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

        $requestStackMock = $this->getRequestStackMock(true);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->promote($username);

        $this->assertSame($username, $user->getUsername());
        $this->assertTrue($user->isSuperAdmin());
    }

    public function testPromoteWithInvalidUsername()
    {
        $this->expectException(\InvalidArgumentException::class);
        $userManagerMock = $this->getMockBuilder('FOS\UserBundle\Model\UserManagerInterface')->getMock();
        $invalidusername = 'invalid_username';

        $userManagerMock->expects($this->once())
            ->method('findUserByUsername')
            ->will($this->returnValue(null))
            ->with($this->equalTo($invalidusername));

        $userManagerMock->expects($this->never())
            ->method('updateUser');

        $eventDispatcherMock = $this->getEventDispatcherMock(FOSUserEvents::USER_PROMOTED, false);

        $requestStackMock = $this->getRequestStackMock(false);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->promote($invalidusername);
    }

    public function testDemoteWithValidUsername()
    {
        $userManagerMock = $this->getMockBuilder('FOS\UserBundle\Model\UserManagerInterface')->getMock();
        $username = 'test_username';

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

        $requestStackMock = $this->getRequestStackMock(true);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->demote($username);

        $this->assertSame($username, $user->getUsername());
        $this->assertFalse($user->isSuperAdmin());
    }

    public function testDemoteWithInvalidUsername()
    {
        $this->expectException(\InvalidArgumentException::class);
        $userManagerMock = $this->getMockBuilder('FOS\UserBundle\Model\UserManagerInterface')->getMock();
        $invalidusername = 'invalid_username';

        $userManagerMock->expects($this->once())
            ->method('findUserByUsername')
            ->will($this->returnValue(null))
            ->with($this->equalTo($invalidusername));

        $userManagerMock->expects($this->never())
            ->method('updateUser');

        $eventDispatcherMock = $this->getEventDispatcherMock(FOSUserEvents::USER_DEMOTED, false);

        $requestStackMock = $this->getRequestStackMock(false);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->demote($invalidusername);
    }

    public function testChangePasswordWithValidUsername()
    {
        $userManagerMock = $this->getMockBuilder('FOS\UserBundle\Model\UserManagerInterface')->getMock();

        $user = new TestUser();
        $username = 'test_username';
        $password = 'test_password';
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

        $requestStackMock = $this->getRequestStackMock(true);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->changePassword($username, $password);

        $this->assertSame($username, $user->getUsername());
        $this->assertSame($password, $user->getPlainPassword());
    }

    public function testChangePasswordWithInvalidUsername()
    {
        $this->expectException(\InvalidArgumentException::class);
        $userManagerMock = $this->getMockBuilder('FOS\UserBundle\Model\UserManagerInterface')->getMock();

        $invalidusername = 'invalid_username';
        $password = 'test_password';

        $userManagerMock->expects($this->once())
            ->method('findUserByUsername')
            ->will($this->returnValue(null))
            ->with($this->equalTo($invalidusername));

        $userManagerMock->expects($this->never())
            ->method('updateUser');

        $eventDispatcherMock = $this->getEventDispatcherMock(FOSUserEvents::USER_PASSWORD_CHANGED, false);

        $requestStackMock = $this->getRequestStackMock(false);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->changePassword($invalidusername, $password);
    }

    public function testAddRole()
    {
        $userManagerMock = $this->getMockBuilder('FOS\UserBundle\Model\UserManagerInterface')->getMock();
        $username = 'test_username';
        $userRole = 'test_role';
        $user = new TestUser();

        $userManagerMock->expects($this->exactly(2))
            ->method('findUserByUsername')
            ->will($this->returnValue($user))
            ->with($this->equalTo($username));

        $eventDispatcherMock = $this->getMockBuilder('Symfony\Contracts\EventDispatcher\EventDispatcherInterface')->getMock();
        $requestStackMock = $this->getRequestStackMock(false);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);

        $this->assertTrue($manipulator->addRole($username, $userRole));
        $this->assertFalse($manipulator->addRole($username, $userRole));
        $this->assertTrue($user->hasRole($userRole));
    }

    public function testRemoveRole()
    {
        $userManagerMock = $this->getMockBuilder('FOS\UserBundle\Model\UserManagerInterface')->getMock();
        $username = 'test_username';
        $userRole = 'test_role';
        $user = new TestUser();
        $user->addRole($userRole);

        $userManagerMock->expects($this->exactly(2))
            ->method('findUserByUsername')
            ->will($this->returnValue($user))
            ->with($this->equalTo($username));

        $eventDispatcherMock = $this->getMockBuilder('Symfony\Contracts\EventDispatcher\EventDispatcherInterface')->getMock();
        $requestStackMock = $this->getRequestStackMock(false);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);

        $this->assertTrue($manipulator->removeRole($username, $userRole));
        $this->assertFalse($user->hasRole($userRole));
        $this->assertFalse($manipulator->removeRole($username, $userRole));
    }

    /**
     * @param string $event
     * @param bool   $once
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getEventDispatcherMock($event, $once = true)
    {
        $eventDispatcherMock = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();

        $eventDispatcherMock->expects($once ? $this->once() : $this->never())
            ->method('dispatch')
            ->withAnyParameters();

        return $eventDispatcherMock;
    }

    /**
     * @param bool $once
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getRequestStackMock($once = true)
    {
        $requestStackMock = $this->getMockBuilder('Symfony\Component\HttpFoundation\RequestStack')->getMock();

        $requestStackMock->expects($once ? $this->once() : $this->never())
            ->method('getCurrentRequest')
            ->willReturn(null);

        return $requestStackMock;
    }
}
