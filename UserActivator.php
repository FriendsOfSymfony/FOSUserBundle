<?php

namespace FOS\UserBundle;

use FOS\UserBundle\Model\User;
use FOS\UserBundle\Model\UserManagerInterface;

/**
 * Activates a given user
 */
class UserActivator
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
     * Activates a given user
     *
     * @param string $username
     */
    public function activate($username)
    {
        $user = $this->userManager->findUserByUsername($username);

        if (!$user) {
            throw new \InvalidArgumentException(sprintf('User identified by "%s" username does not exist.', $username));
        }
        $user->setEnabled(true);
        $this->userManager->updateUser($user);
    }

}
