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
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\RememberMe\RememberMeServicesInterface;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;

/**
 * Abstracts process for manually logging in a user.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class LoginManager implements LoginManagerInterface
{
    private $tokenStorage;
    private $securityContext;
    private $userChecker;
    private $sessionStrategy;
    private $container;

    public function __construct(UserCheckerInterface $userChecker,
                                SessionAuthenticationStrategyInterface $sessionStrategy,
                                ContainerInterface $container)
    {
        $this->userChecker = $userChecker;
        $this->sessionStrategy = $sessionStrategy;
        $this->container = $container;
    }

    final public function loginUser($firewallName, UserInterface $user, Response $response = null)
    {
        $this->userChecker->checkPostAuth($user);

        $token = $this->createToken($firewallName, $user);

        if ($this->container->has("request")) {
            $request = $this->container->get("request");
        } elseif ($this->container->has("request_stack")) {
            $request = $this->container->get("request_stack")->getCurrentRequest();
        } else {
            $request = null;
        }
        if ($request) {
            $this->sessionStrategy->onAuthentication($request, $token);

            if (null !== $response) {
                $rememberMeServices = null;
                if ($this->container->has('security.authentication.rememberme.services.persistent.'.$firewallName)) {
                    $rememberMeServices = $this->container->get('security.authentication.rememberme.services.persistent.'.$firewallName);
                } elseif ($this->container->has('security.authentication.rememberme.services.simplehash.'.$firewallName)) {
                    $rememberMeServices = $this->container->get('security.authentication.rememberme.services.simplehash.'.$firewallName);
                }

                if ($rememberMeServices instanceof RememberMeServicesInterface) {
                    $rememberMeServices->loginSuccess($request, $response, $token);
                }
            }
        }

        if ($this->tokenStorage) {
            $this->tokenStorage->setToken($token);
        } elseif ($this->securityContext) {
            $this->securityContext->setToken($token);
        }
    }
    
    public function setTokenStorage(TokenStorage $tokenStorage = null)
    {
        $this->tokenStorage = $tokenStorage;
    }
    
    public function setSecurityContext(SecurityContextInterface $securityContext = null)
    {
        $this->securityContext = $securityContext;
    }
    
    protected function createToken($firewall, UserInterface $user)
    {
        return new UsernamePasswordToken($user, null, $firewall, $user->getRoles());
    }
}
