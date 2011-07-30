<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Security\Core\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use FOS\UserBundle\Security\Core\Authentication\Token\OAuthToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
/**
 * @author   Marcel Beerta <marcel@etcpasswd.de>
 */
class OAuthProvider implements AuthenticationProviderInterface
{
    private $userProvider;

    public function __construct(UserProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    public function authenticate(TokenInterface $token)
    {
        $user = $this->userProvider->loadUserByUsername($token->getUsername());

        if($user ) {
            // Todo: token validation
            $token->setUser($user);
            return $token;
        }

        throw new AuthenticationException('OAuth Authentication Failed.');
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof OAuthToken;
    }

}