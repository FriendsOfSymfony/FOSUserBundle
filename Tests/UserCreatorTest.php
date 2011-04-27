<?php

namespace FOS\UserBundle;

use FOS\UserBundle\UserCreator;

class UserCreatorTest extends \PHPUnit_Framework_TestCase
{
    public function testUserCreator()
    {
        // create userManagerMock mock object
        $userManagerMock = $this->createUserManagerMock(array());

        $user = new User();

        // now configuring the mock object userManagerMock
        $userManagerMock->expects($this->once())
            ->method('createUser')
            ->will($this->returnValue($user));

        // calling the class and not the container - remember isolation
        $creator = new UserCreator($userManagerMock);

        // experiment
        $username = 'test_username';
        $password = 'test_password';
        $inactive = false; // it is enabled
        $superadmin = false;
        $creator->create($username, $password, $email, $inactive, $superadmin);

        // testing output of experiment
        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals($password, $user->getPlainPassword());
        $this->assertEquals($inactive, $user->isEnabled());
        $this->assertEquals($superadmin, $user->isSuperAdmin());

    }

    protected function createUserManagerMock(array $methods)
    {
        $userManager = $this->getMock('FOS\UserBundle\Entity\UserManager', $methods, array(), '', false);

        return $userManager;
    }

}
