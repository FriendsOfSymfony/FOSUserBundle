<?php

namespace FOS\UserBundle;

use FOS\UserBundle\UserCreator;

class UserCreatorTest extends \PHPUnit_Framework_TestCase
{
    public function testUserCreator()
    {
        // create userManagerMock mock object
        $userManagerMock = $this->createUserManagerMock(array());

        // now configuring the mock object userManagerMock
        $userManagerMock->expects($this->once())
            ->method('createUser')
            ->will($this->returnValue(new User()));

        // ? should it be instead $this->container->get('fos_user:user_creator');
        $creator = new UserCreator($userManagerMock);

        $username = 'test_username';
        $password = 'test_password';
        $inactive = 'test_inactive';
        $superadmin = 'test_superadmin';

        // experiment
        $user = $creator->create($username, $password, $email, $inactive, $superadmin);

        // testing output of experiment
        $this->assertEquals($username, $user->getUsername());

        return $user;

    }

    protected function createUserManagerMock(array $methods)
    {
        $userManager = $this->getMock('FOS\UserBundle\Entity\UserManager', $methods, array(), '', false);

        return $userManager;
    }

}
