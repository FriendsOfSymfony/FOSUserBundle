<?php

namespace FOS\UserBundle;

use FOS\UserBundle\Model\User;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Dbal\AclProvider;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;

/**
 * Installs Access Control Entities
 */
class AcesInstaller
{
    /**
     * User manager
     *
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * Acl provider
     *
     * @var AclProvider
     */
    protected $aclProvider;

    public function __construct(UserManagerInterface $userManager, AclProvider $aclProvider = null )
    {
        $this->userManager = $userManager;
        $this->aclProvider = $aclProvider;
    }

    /**
     * Installs Aces a user and its Acl
     */
    public function install()
    {
        $oid = new ObjectIdentity('class', $this->userManager->getClass());
        $acl = $this->aclProvider->createAcl($oid);

        // insert ACEs for the super admin
        $sid = new RoleSecurityIdentity(User::ROLE_SUPERADMIN);
        $acl->insertClassAce($sid, MaskBuilder::MASK_IDDQD);
        $this->aclProvider->updateAcl($acl);
    }

    // TODO: there is another check like this raw $this->aclProvider but was in UserCreator I think needs to be of the form of isset
    public function hasAclProvider()
    {
        return isset($this->aclProvider)? true: false;
    }
}
