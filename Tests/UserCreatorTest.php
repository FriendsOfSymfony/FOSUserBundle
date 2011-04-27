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

        // ? should it be instead $this->container->get('fos_user:user_creator');
        $creator = new UserCreator($userManagerMock);

        // experiment
        $username = 'test_username';
        $password = 'test_password';
        $inactive = 'test_inactive';
        $superadmin = 'test_superadmin';
        $creator->create($username, $password, $email, $inactive, $superadmin);

        // testing output of experiment
        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals($password, $user->getPassword());
        $this->assertEquals($inactive, $user->getInactive());
        $this->assertEquals($superadmin, $user->getSuperadmin());

        return $user;

    }

    protected function createUserManagerMock(array $methods)
    {
        $userManager = $this->getMock('FOS\UserBundle\Entity\UserManager', $methods, array(), '', false);

        return $userManager;
    }

}
