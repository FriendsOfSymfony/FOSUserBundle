<?php

namespace FOS\UserBundle\Tests;

use FOS\UserBundle\UserPasswordChanger;
use FOS\UserBundle\Tests\TestUser;
use Symfony\Component\Security\Acl\Domain\Acl;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\PermissionGrantingStrategy;

class UserPasswordChangerTest extends \PHPUnit_Framework_TestCase
{
    public function testChange()
    {
        $userManagerMock = $this->createUserManagerMock(array('createUser', 'updateUser'));

        $user = new TestUser();
        $user->setId(77);

        $username    = 'test_username';
        $password    = 'test_password';
        $oldpassword = 'old_password';

        $user->setUsername($username);
        $user->setPlainPassword($oldpassword);

        $userManagerMock->expects($this->once())
            ->method('findUserByUsername')
            ->will($this->returnValue($user))
            ->with($this->equals($username));

        $userManagerMock->expects($this->once())
            ->method('updateUser')
            ->will($this->returnValue($user))
            ->with($this->isInstanceOf('FOS\UserBundle\Tests\TestUser'));

        $changer = new UserPasswordChanger($userManagerMock);

        $changer->change($username, $password);

        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals($password, $user->getPlainPassword());

    }

    protected function createUserManagerMock(array $methods)
    {
        $userManager = $this->getMockBuilder('FOS\UserBundle\Entity\UserManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        return $userManager;
    }

}
