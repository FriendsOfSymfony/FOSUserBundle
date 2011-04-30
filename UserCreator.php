<?php

namespace FOS\UserBundle;

use FOS\UserBundle\Model\User;
use FOS\UserBundle\Entity\UserManager;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Dbal\AclProvider;

class UserCreator
{
    protected $userManager;
    protected $aclProvider;

    public function __construct(UserManager $userManager, AclProvider $aclProvider = null )
    {
        $this->userManager = $userManager;
        $this->aclProvider = $aclProvider;
    }

    public function create($username, $password, $email, $inactive, $superadmin)
    {
        $user = $this->userManager->createUser();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPlainPassword($password);
        $user->setEnabled(!$inactive);
        $user->setSuperAdmin($superadmin);
        $this->userManager->updateUser($user);

        if ($this->aclProvider != null) {
            $oid = ObjectIdentity::fromDomainObject($user);
            $acl = $this->aclProvider->createAcl($oid);
            $acl->insertObjectAce(UserSecurityIdentity::fromAccount($user), MaskBuilder::MASK_OWNER);
            $this->aclProvider->updateAcl($acl);
        }
    }
}
