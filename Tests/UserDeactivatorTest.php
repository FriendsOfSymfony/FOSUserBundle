<?php

namespace FOS\UserBundle\Tests;

use FOS\UserBundle\UserDeactivator;
use FOS\UserBundle\Tests\TestUser;

class UserActivatorTest extends \PHPUnit_Framework_TestCase
{
    public function testDeactivateWithValidUsername()
    {
        $userManagerMock = $this->createUserManagerMock(array('findUserByUsername', 'updateUser'));

        $user = new TestUser();
        $user->setId(77);

        $username    = 'test_username';

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

        $deactivator = new UserDeactivator($userManagerMock);

        $deactivator->deactivate($username);

        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals(false, $user->isEnabled());

    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDeactivateWithInvalidUsername()
    {
        $userManagerMock = $this->createUserManagerMock(array('findUserByUsername', 'updateUser'));

        $user = new TestUser();
        $user->setId(77);

        $username    = 'test_username';
        $invalidusername    = 'invalid_username';

        $user->setUsername($username);
        $user->setEnabled(true);

        $userManagerMock->expects($this->once())
            ->method('findUserByUsername')
            ->will($this->returnValue(null))
            ->with($this->equalTo($invalidusername));

        $userManagerMock->expects($this->never())
            ->method('updateUser');

        $deactivator = new UserDeactivator($userManagerMock);

        $deactivator->deactivate($invalidusername);

    }

    protected function createUserManagerMock(array $methods)
    {
        $userManager = $this->getMockBuilder('FOS\UserBundle\Model\UserManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        return $userManager;
    }

}
