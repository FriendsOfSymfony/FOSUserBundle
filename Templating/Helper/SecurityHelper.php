<?php

namespace FOS\UserBundle\Templating\Helper;

use Symfony\Bundle\SecurityBundle\Templating\Helper\SecurityHelper as BaseSecurityHelper;
use FOS\UserBundle\Model\User;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * SecurityHelper.
 */
class SecurityHelper extends BaseSecurityHelper
{
    /**
     * Returns the authenticated user, if any
     *
     * @return FOS\UserBundle\Model\User
     */
    public function getUser()
    {
        return $this->context->getToken()->getUser();
    }

    /**
     * Tells whether the authenticated user is this user
     *
     * @return bool
     **/
    public function isUser(User $user)
    {
        $authenticatedUser = $this->getUser();

        return $authenticatedUser instanceof User && $authenticatedUser->isUser($user);
    }

    /**
     * Tell whether or not a user is logged in
     *
     * @return bool
     **/
    public function isAuthenticated()
    {
        return null !== $this->context->getToken();
    }

    /**
     * Tell whether or not the user is anonymous
     *
     * @return bool
     **/
    public function isAnonymous()
    {
        return false === $this->vote('IS_AUTHENTICATED_FULLY');
    }

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     */
    public function getName()
    {
        return 'fos_user_security';
    }
}
