<?php

namespace FOS\UserBundle\Tests;

use FOS\UserBundle\UserDemoter;
use FOS\UserBundle\Tests\TestUser;

class UserDemoterTest extends \PHPUnit_Framework_TestCase
{
    public function testDemoteWithValidUsername()
    {
        $userManagerMock = $this->createUserManagerMock(array('findUserByUsername', 'updateUser'));

        $user = new TestUser();
        $user->setId(77);

        $username    = 'test_username';

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

        $demoter = new UserDemoter($userManagerMock);

        $demoter->demote($username);

        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals(false, $user->isSuperAdmin());

    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDemoteWithInvalidUsername()
    {
        $userManagerMock = $this->createUserManagerMock(array('findUserByUsername', 'updateUser'));

        $user = new TestUser();
        $user->setId(77);

        $username    = 'test_username';
        $invalidusername    = 'invalid_username';

        $user->setUsername($username);
        $user->setSuperAdmin(true);

        $userManagerMock->expects($this->once())
            ->method('findUserByUsername')
            ->will($this->returnValue(null))
            ->with($this->equalTo($invalidusername));

        $userManagerMock->expects($this->never())
            ->method('updateUser');

        $demoter = new UserDemoter($userManagerMock);

        $demoter->demote($invalidusername);

    }

    protected function createUserManagerMock(array $methods)
    {
        $userManager = $this->getMockBuilder('FOS\UserBundle\Model\UserManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        return $userManager;
    }

}
