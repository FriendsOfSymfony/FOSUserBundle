<?php

namespace FOS\UserBundle;

use FOS\UserBundle\UserCreator;

class UserCreatorTest extends \PHPUnit_Framework_TestCase
{
    public function testUserCreator()
    {
        $userManagerMock = $this->createUserManagerMock(array());

        // ? should it be instead $this->container->get('fos_user:user_creator');
        $creator = new UserCreator($userManagerMock);

        $username = 'test_username';
        $password = 'test_password';
        $inactive = 'test_inactive';
        $superadmin = 'test_superadmin';

        $user = $creator->create($username, $password, $email, $inactive, $superadmin);

        $creator->expects($this->once()) //$game->expects($this->never())
            ->method('findCancelableByUser')
            ->with($user)
            ->will($this->returnValue($games));
            ->with($user, User::STARTING_ELO);

        return $user;

    }

    protected function createUserManagerMock(array $methods)
    {
        $userManager = $this->getMock('FOS\UserBundle\Entity\UserManager', $methods, array(), '', false);

        return $userManager;
    }

}
