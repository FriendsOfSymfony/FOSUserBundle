<?php

namespace FOS\UserBundle;

use FOS\UserBundle\Model\User;
use FOS\UserBundle\Model\UserManagerInterface;

/**
 * Demotes a given user
 */
class UserDemoter
{
    /**
     * User manager
     *
     * @var UserManagerInterface
     */
    protected $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * Demotes given user
     *
     * @param string $username
     */
    public function demote($username)
    {
        $user = $this->userManager->findUserByUsername($username);

        if (!$user) {
            throw new \InvalidArgumentException(sprintf('User identified by "%s" username does not exist.', $username));
        }
        $user->setSuperAdmin(false);
        $this->userManager->updateUser($user);
    }

}
