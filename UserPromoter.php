<?php

namespace FOS\UserBundle;

use FOS\UserBundle\Model\User;
use FOS\UserBundle\Model\UserManagerInterface;

/**
 * Promotes a given user
 */
class UserPromoter
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
     * Promotes given user
     *
     * @param string $username
     */
    public function promote($username)
    {
        $user = $this->userManager->findUserByUsername($username);

        if (!$user) {
            throw new \InvalidArgumentException(sprintf('User identified by "%s" username does not exist.', $username));
        }
        $user->setSuperAdmin(true);
        $this->userManager->updateUser($user);
    }

}
