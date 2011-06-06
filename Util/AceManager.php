<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Util;

use FOS\UserBundle\Model\User;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;

/**
 * Installs Access Control Entities
 *
 * @author Christophe Coevoet <stof@notk.org>
 * @author Luis Cordova <cordoval@gmail.com>
 */
class AceManager
{
    /**
     * Acl provider
     *
     * @var MutableAclProviderInterface
     */
    protected $aclProvider;

    public function __construct(MutableAclProviderInterface $aclProvider = null )
    {
        $this->aclProvider = $aclProvider;
    }

    /**
     * Installs the class ACE for the superadmin.
     *
     * @param string $class The class name for which to set the class ACE
     */
    public function installAces($class)
    {
        if (!$this->aclProvider) {
            return;
        }
        $oid = new ObjectIdentity('class', $class);
        $acl = $this->aclProvider->createAcl($oid);

        // insert ACEs for the super admin
        $sid = new RoleSecurityIdentity(User::ROLE_SUPER_ADMIN);
        $acl->insertClassAce($sid, MaskBuilder::MASK_IDDQD);
        $this->aclProvider->updateAcl($acl);
    }

    // TODO: there is another check like this raw $this->aclProvider but was in UserCreator I think needs to be of the form of isset
    public function hasAclProvider()
    {
        return isset($this->aclProvider);
    }

    /**
     * Creates the ACE for a user.
     *
     * @param UserInterface $user
     */
    public function createUserAce(UserInterface $user)
    {
        if (!$this->aclProvider) {
            return;
        }
        $oid = ObjectIdentity::fromDomainObject($user);
        $acl = $this->aclProvider->createAcl($oid);
        $acl->insertObjectAce(UserSecurityIdentity::fromAccount($user), MaskBuilder::MASK_OWNER);
        $this->aclProvider->updateAcl($acl);
    }
}
