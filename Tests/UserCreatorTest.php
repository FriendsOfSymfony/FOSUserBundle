<?php

namespace FOS\UserBundle\Tests;

use FOS\UserBundle\UserCreator;
use FOS\UserBundle\Tests\TestUser;
use Symfony\Component\Security\Acl\Domain\Acl;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\PermissionGrantingStrategy;

class UserCreatorTest extends \PHPUnit_Framework_TestCase
{

    private $userManagerMock;
    private $aclProviderMock;
    private $user;
    private $acl;
    private $username;
    private $password;
    private $email;
    private $inactive;
    private $superadmin;

    public function setUp()
    {
        // create userManagerMock mock object
        $this->userManagerMock = $this->createUserManagerMock(array('createUser', 'updateUser'));

        // create provider mock object
        $this->aclProviderMock = $this->createProviderMock(array('createAcl', 'updateAcl'));
        
        $this->user = new TestUser();
        $this->user->setId(77);

        $objectIdentity = new ObjectIdentity('exampleidentifier','userperhaps');
        $permissionGrantingStrategy = new PermissionGrantingStrategy();
        $this->acl = new Acl(1,$objectIdentity,$permissionGrantingStrategy,array(),true);

        $this->username = 'test_username';
        $this->password = 'test_password';
        $this->email = 'test@email.org';
        $this->inactive = false; // it is enabled
        $this->superadmin = false;

    }

    public function testUserCreator()
    {
        $this->userManagerMock->expects($this->once())
            ->method('createUser')
            ->will($this->returnValue($this->user));

        $this->userManagerMock->expects($this->once())
            ->method('updateUser')
            ->will($this->returnValue($this->user))
            ->with($this->isInstanceOf('FOS\UserBundle\Tests\TestUser'));

        $this->aclProviderMock->expects($this->once())
            ->method('createAcl')
            ->will($this->returnValue($this->acl))
            ->with($this->isInstanceOf('Symfony\Component\Security\Acl\Model\ObjectIdentityInterface'));

        $this->aclProviderMock->expects($this->once())
            ->method('updateAcl')
            ->with($this->isInstanceOf('Symfony\Component\Security\Acl\Domain\Acl'));

        $creator = new UserCreator($this->userManagerMock, $this->aclProviderMock);

        // experiment
        $creator->create( $this->username,
                          $this->password,
                          $this->email,
                          $this->inactive,
                          $this->superadmin );

        $this->assertEquals($this->username, $this->user->getUsername());
        $this->assertEquals($this->password, $this->user->getPlainPassword());
        $this->assertEquals($this->email, $this->user->getEmail());
        $this->assertEquals($this->inactive, !$this->user->isEnabled());
        $this->assertEquals($this->superadmin, $this->user->isSuperAdmin());

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
