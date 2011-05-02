<?php

namespace FOS\UserBundle\Tests;

use FOS\UserBundle\UserCreator;
use FOS\UserBundle\Tests\TestUser;
use Symfony\Component\Security\Acl\Domain\Acl;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\PermissionGrantingStrategy;

class UserCreatorTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        // create userManagerMock mock object
        $userManagerMock = $this->createUserManagerMock(array('createUser', 'updateUser'));

        // create provider mock object
        $aclProviderMock = $this->createProviderMock(array('createAcl', 'updateAcl'));

        $user = new TestUser();
        $user->setId(77);

        $objectIdentity = new ObjectIdentity('exampleidentifier','userperhaps');
        $permissionGrantingStrategy = new PermissionGrantingStrategy();
        $acl = new Acl(1,$objectIdentity,$permissionGrantingStrategy,array(),true);

        $username = 'test_username';
        $password = 'test_password';
        $email = 'test@email.org';
        $inactive = false; // it is enabled
        $superadmin = false;

        $userManagerMock->expects($this->once())
            ->method('createUser')
            ->will($this->returnValue($user));

        $userManagerMock->expects($this->once())
            ->method('updateUser')
            ->will($this->returnValue($user))
            ->with($this->isInstanceOf('FOS\UserBundle\Tests\TestUser'));

        $aclProviderMock->expects($this->once())
            ->method('createAcl')
            ->will($this->returnValue($acl))
            ->with($this->isInstanceOf('Symfony\Component\Security\Acl\Model\ObjectIdentityInterface'));

        $aclProviderMock->expects($this->once())
            ->method('updateAcl')
            ->with($this->isInstanceOf('Symfony\Component\Security\Acl\Domain\Acl'));

        $creator = new UserCreator($userManagerMock, $aclProviderMock);

        $creator->create($username, $password, $email, $inactive, $superadmin);

        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals($password, $user->getPlainPassword());
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($inactive, !$user->isEnabled());
        $this->assertEquals($superadmin, $user->isSuperAdmin());

    }

    protected function createUserManagerMock(array $methods)
    {
        return $this->getMockBuilder('FOS\UserBundle\Model\UserManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        return $userManager;
    }

    protected function createProviderMock(array $methods)
    {
        return $this->getMock('Symfony\Component\Security\Acl\Dbal\AclProvider', $methods, array(), '', false);
    }

}
