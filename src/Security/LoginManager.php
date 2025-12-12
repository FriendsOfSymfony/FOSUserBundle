<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Security;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Http\RememberMe\RememberMeHandlerInterface;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;

/**
 * Abstracts process for manually logging in a user.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class LoginManager implements LoginManagerInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var UserCheckerInterface
     */
    private $userChecker;

    /**
     * @var SessionAuthenticationStrategyInterface
     */
    private $sessionStrategy;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var RememberMeHandlerInterface|null
     */
    private $rememberMeHandler;

    public function __construct(TokenStorageInterface $tokenStorage, UserCheckerInterface $userChecker,
        SessionAuthenticationStrategyInterface $sessionStrategy,
        RequestStack $requestStack,
        ?RememberMeHandlerInterface $rememberMeHandler = null,
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->userChecker = $userChecker;
        $this->sessionStrategy = $sessionStrategy;
        $this->requestStack = $requestStack;
        $this->rememberMeHandler = $rememberMeHandler;
    }

    final public function logInUser($firewallName, UserInterface $user, ?Response $response = null): void
    {
        $this->userChecker->checkPreAuth($user);

        $token = $this->createToken($firewallName, $user);
        $request = $this->requestStack->getCurrentRequest();

        if (null !== $request) {
            $this->sessionStrategy->onAuthentication($request, $token);

            if ($this->rememberMeHandler instanceof RememberMeHandlerInterface) {
                $this->rememberMeHandler->createRememberMeCookie($user);
            }
        }

        $this->tokenStorage->setToken($token);
    }

    /**
     * @param string $firewall
     */
    protected function createToken($firewall, UserInterface $user): UsernamePasswordToken
    {
        return new UsernamePasswordToken($user, $firewall, $user->getRoles());
    }
}
