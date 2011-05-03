<?php

namespace FOS\UserBundle\Tests;

use FOS\UserBundle\UserPromoter;
use FOS\UserBundle\Tests\TestUser;

class UserPromoterTest extends \PHPUnit_Framework_TestCase
{
    public function testPromoteWithValidUsername()
    {
        $userManagerMock = $this->createUserManagerMock(array('findUserByUsername', 'updateUser'));

        $user = new TestUser();
        $user->setId(77);

        $username    = 'test_username';

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

        $promoter = new UserPromoter($userManagerMock);

        $promoter->promote($username);

        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals(true, $user->isSuperAdmin());

    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testPromoteWithInvalidUsername()
    {
        $userManagerMock = $this->createUserManagerMock(array('findUserByUsername', 'updateUser'));

        $user = new TestUser();
        $user->setId(77);

        $username    = 'test_username';
        $invalidusername    = 'invalid_username';

        $user->setUsername($username);
        $user->setSuperAdmin(false);

        $userManagerMock->expects($this->once())
            ->method('findUserByUsername')
            ->will($this->returnValue(null))
            ->with($this->equalTo($invalidusername));

        $userManagerMock->expects($this->never())
            ->method('updateUser');

        $promoter = new UserPromoter($userManagerMock);

        $promoter->promote($invalidusername);

    }

    protected function createUserManagerMock(array $methods)
    {
        $userManager = $this->getMockBuilder('FOS\UserBundle\Model\UserManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        return $userManager;
    }

}
