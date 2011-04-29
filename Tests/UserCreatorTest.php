<?php

namespace FOS\UserBundle\Tests;

use FOS\UserBundle\UserCreator;
use FOS\UserBundle\Tests\TestUser;

class UserCreatorTest extends \PHPUnit_Framework_TestCase
{
    public function testUserCreator()
    {
        // create userManagerMock mock object
        $userManagerMock = $this->createUserManagerMock(array());

        // create provider mock object
        $aclProviderMock = $this->createProviderMock(array());

        $user = new TestUser();
        $user->setId(77);
        $acl = new ACL();
        
        // now configuring the mock object userManagerMock
        $userManagerMock->expects($this->once())
            ->method('createUser')
            ->will($this->returnValue($user));

        // also checks that updateUser gets called
        // also makes sure that the mock manager returns values set for other fields of $user object
        $userManagerMock->expects($this->once())
            ->method('updateUser')
            ->will($this->returnValue($user))
            ->with($this->isInstanceOf('FOS\UserBundle\Tests\TestUser'));

        // now configuring the mock object providerMock
        $aclProviderMock->expects($this->once())
            ->method('createAcl')
            ->will($this->returnValue($acl))
            ->with($this->isInstanceOf('Symfony\Component\Security\Acl\Model\ObjectIdentityInterface'));

        // calling the class and not the container - remember isolation
        $creator = new UserCreator($userManagerMock, $providerMock);

        // experiment
        $username = 'test_username';
        $password = 'test_password';
        $email = 'test@email.org';
        $inactive = false; // it is enabled
        $superadmin = false;
        $creator->create($username, $password, $email, $inactive, $superadmin);

        // testing output of experiment
        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals($password, $user->getPlainPassword());
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($inactive, !$user->isEnabled());
        $this->assertEquals($superadmin, $user->isSuperAdmin());

    }

    protected function createUserManagerMock(array $methods)
    {
        $userManager = $this->getMock('FOS\UserBundle\Entity\UserManager', $methods, array(), '', false);

        return $userManager;
    }

    protected function createProviderMock(array $methods)
    {
        $aclProvider = $this->getMock('Symfony\Component\Security\Acl\Dbal\AclProvider', $methods, array(), '', false);

        return $aclProvider;
    }

}
