<?php

namespace FOS\UserBundle;

use FOS\UserBundle\Model\User;
use FOS\UserBundle\Model\UserManagerInterface;

/**
 * Deactivates a given user
 */
class UserDeactivator
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
     * Deactivates given user
     *
     * @param string $username
     */
    public function deactivate($username)
    {
        $user = $this->userManager->findUserByUsername($username);

        if (!$user) {
            throw new \InvalidArgumentException(sprintf('User identified by "%s" username does not exist.', $username));
        }
        $user->setEnabled(false);
        $this->userManager->updateUser($user);
    }

}
