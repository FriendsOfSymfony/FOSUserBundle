<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Tests\Util;

use FOS\UserBundle\Util\AceManager;
use FOS\UserBundle\Tests\TestUser;
use Symfony\Component\Security\Acl\Domain\Acl;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\PermissionGrantingStrategy;

class AceManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testInstall()
    {
        $aclProviderMock = $this->getMock('Symfony\Component\Security\Acl\Model\MutableAclProviderInterface');
        $user = new TestUser();

        $objectIdentity = new ObjectIdentity('exampleidentifier','userperhaps');
        $permissionGrantingStrategy = new PermissionGrantingStrategy();
        $acl = new Acl(1,$objectIdentity,$permissionGrantingStrategy,array(),true);

        $aclProviderMock->expects($this->once())
            ->method('createAcl')
            ->will($this->returnValue($acl))
            ->with($this->isInstanceOf('Symfony\Component\Security\Acl\Model\ObjectIdentityInterface'));

        $aclProviderMock->expects($this->once())
            ->method('updateAcl')
            ->with($this->isInstanceOf('Symfony\Component\Security\Acl\Domain\Acl'));

        $aceManager = new AceManager($aclProviderMock);
        $aceManager->installAces('someclass');
    }

    public function testCreateUserAce()
    {
        $aclProviderMock = $this->getMock('Symfony\Component\Security\Acl\Model\MutableAclProviderInterface');
        $user = new TestUser();
        $user->setId(42);
        $user->setUsername('test_user');

        $objectIdentity = new ObjectIdentity('exampleidentifier','userperhaps');
        $permissionGrantingStrategy = new PermissionGrantingStrategy();
        $acl = new Acl(1,$objectIdentity,$permissionGrantingStrategy,array(),true);

        $aclProviderMock->expects($this->once())
            ->method('createAcl')
            ->will($this->returnValue($acl))
            ->with($this->isInstanceOf('Symfony\Component\Security\Acl\Model\ObjectIdentityInterface'));

        $aclProviderMock->expects($this->once())
            ->method('updateAcl')
            ->with($this->isInstanceOf('Symfony\Component\Security\Acl\Domain\Acl'));

        $aceManager = new AceManager($aclProviderMock);
        $aceManager->createUserAce($user);
    }
}
