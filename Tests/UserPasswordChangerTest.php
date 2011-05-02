<?php

namespace FOS\UserBundle\Tests;

use FOS\UserBundle\UserPasswordChanger;
use FOS\UserBundle\Tests\TestUser;

class UserPasswordChangerTest extends \PHPUnit_Framework_TestCase
{
    public function testChangeWithValidUsername()
    {
        $userManagerMock = $this->createUserManagerMock(array('findUserByUsername', 'updateUser'));

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
            ->with($this->equalTo($username));

        $userManagerMock->expects($this->once())
            ->method('updateUser')
            ->will($this->returnValue($user))
            ->with($this->isInstanceOf('FOS\UserBundle\Tests\TestUser'));

        $changer = new UserPasswordChanger($userManagerMock);

        $changer->change($username, $password);

        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals($password, $user->getPlainPassword());

    }

    public function testChangeWithInvalidUsername()
    {
        $userManagerMock = $this->createUserManagerMock(array('findUserByUsername', 'updateUser'));

        $user = new TestUser();
        $user->setId(77);

        $username         = 'test_username';
        $invalidusername  = 'invalid_username';
        $password         = 'test_password';
        $oldpassword      = 'old_password';

        $user->setUsername($username);
        $user->setPlainPassword($oldpassword);

        $userManagerMock->expects($this->once())
            ->method('findUserByUsername')
            ->will($this->returnValue(null))
            ->with($this->equalTo($invalidusername));

        $userManagerMock->expects($this->never())
            ->method('updateUser');

        $changer = new UserPasswordChanger($userManagerMock);

        try {
            $changer->change($invalidusername, $password);
        }

        catch (\InvalidArgumentException $expected) {
            return;
        }

        $this->fail('An expected exception has not been raised.');

    }

    protected function createUserManagerMock(array $methods)
    {
        $userManager = $this->getMockBuilder('FOS\UserBundle\Model\UserManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        return $userManager;
    }

}
