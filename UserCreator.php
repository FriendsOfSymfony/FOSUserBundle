<?php

namespace FOS\UserBundle;

use FOS\UserBundle\Model\User;
use FOS\UserBundle\Entity\UserManager;

class UserCreator
{
    protected $userManager;
    protected $provider;

    public function __construct(UserManager $userManager, ACLProvider $provider)
    {
        $this->userManager = $userManager;
        $this->provider = $provider';
    }

    public function create($username, $password, $email, $inactive, $superadmin)
    {
        $user = $this->userManager->createUser();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPlainPassword($password);
        $user->setEnabled(!$inactive);
        $user->setSuperAdmin(!!$superadmin);
        $this->userManager->updateUser($user);

        if ($this->provider)) {
            $oid = ObjectIdentity::fromDomainObject($user);
            $acl = $this->provider->createAcl($oid);
            $acl->insertObjectAce(UserSecurityIdentity::fromAccount($user), MaskBuilder::MASK_OWNER);
            $this->provider->updateAcl($acl);
        }
    }
}
