<?php

namespace FOS\UserBundle;

use FOS\UserBundle\Model\User;
use FOS\UserBundle\Entity\UserManager;

class UserCreator
{
    protected $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
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

    }
}
