<?php

namespace FOS\UserBundle\Util;

use FOS\UserBundle\Model\User;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Dbal\AclProvider;

/**
 * Creates a user and its Acl
 */
class UserCreator
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
     * Creates a user and its Acl
     *
     * @param string $username
     * @param string $password
     * @param string $email
     * @param boolean $active
     * @param boolean $superadmin
     */
    public function create($username, $password, $email, $active, $superadmin)
    {
        $user = $this->userManager->createUser();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPlainPassword($password);
        $user->setEnabled($active);
        $user->setSuperAdmin((bool)$superadmin);
        $this->userManager->updateUser($user);

        $this->createAcl($user);
    }

    /**
     * Creates the user ACL *if* an acl provider is available
     *
     * @return null
     **/
    public function createAcl(User $user)
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
