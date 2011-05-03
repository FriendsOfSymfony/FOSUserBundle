<?php

namespace FOS\UserBundle\Tests;

use FOS\UserBundle\UserActivator;
use FOS\UserBundle\Tests\TestUser;

class UserActivatorTest extends \PHPUnit_Framework_TestCase
{
    public function testActivateWithValidUsername()
    {
        $userManagerMock = $this->createUserManagerMock(array('findUserByUsername', 'updateUser'));

        $user = new TestUser();
        $user->setId(77);

        $username    = 'test_username';

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

        $activator = new UserActivator($userManagerMock);

        $activator->activate($username);

        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals(true, $user->isEnabled());

    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testActivateWithInvalidUsername()
    {
        $userManagerMock = $this->createUserManagerMock(array('findUserByUsername', 'updateUser'));

        $user = new TestUser();
        $user->setId(77);

        $username    = 'test_username';
        $invalidusername    = 'invalid_username';

        $user->setUsername($username);
        $user->setEnabled(false);

        $userManagerMock->expects($this->once())
            ->method('findUserByUsername')
            ->will($this->returnValue(null))
            ->with($this->equalTo($invalidusername));

        $userManagerMock->expects($this->never())
            ->method('updateUser');

        $activator = new UserActivator($userManagerMock);

        $activator->activate($invalidusername);

    }

    protected function createUserManagerMock(array $methods)
    {
        $userManager = $this->getMockBuilder('FOS\UserBundle\Model\UserManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        return $userManager;
    }

}
