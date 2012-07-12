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

        $manipulator = new UserManipulator($userManagerMock);
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

        $manipulator = new UserManipulator($userManagerMock);
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

        $manipulator = new UserManipulator($userManagerMock);
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

        $manipulator = new UserManipulator($userManagerMock);
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

        $manipulator = new UserManipulator($userManagerMock);
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

        $manipulator = new UserManipulator($userManagerMock);
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

        $manipulator = new UserManipulator($userManagerMock);
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

        $manipulator = new UserManipulator($userManagerMock);
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

        $manipulator = new UserManipulator($userManagerMock);
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

        $manipulator = new UserManipulator($userManagerMock);
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

        $manipulator = new UserManipulator($userManagerMock);
        $manipulator->changePassword($invalidusername, $password);
    }
}
