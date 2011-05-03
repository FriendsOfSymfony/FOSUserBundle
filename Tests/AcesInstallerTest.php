<?php

namespace FOS\UserBundle\Tests;

use FOS\UserBundle\AcesInstaller;
use FOS\UserBundle\Tests\TestUser;
use Symfony\Component\Security\Acl\Domain\Acl;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\PermissionGrantingStrategy;

class AcesInstallerTest extends \PHPUnit_Framework_TestCase
{
    public function testInstall()
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

        $userManagerMock->expects($this->once())
            ->method('getClass')
            ->will($this->returnValue('someclass'));

        $aclProviderMock->expects($this->once())
            ->method('createAcl')
            ->will($this->returnValue($acl))
            ->with($this->isInstanceOf('Symfony\Component\Security\Acl\Model\ObjectIdentityInterface'));

        $aclProviderMock->expects($this->once())
            ->method('updateAcl')
            ->with($this->isInstanceOf('Symfony\Component\Security\Acl\Domain\Acl'));

        $installer = new AcesInstaller($userManagerMock, $aclProviderMock);

        $installer->install();

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
