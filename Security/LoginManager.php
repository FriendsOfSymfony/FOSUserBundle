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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\RememberMe\AbstractRememberMeServices;
use Symfony\Component\Security\Http\RememberMe\RememberMeServicesInterface;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;

/**
 * Abstracts process for manually logging in a user.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class LoginManager implements LoginManagerInterface
{
    private $securityContext;
    private $userChecker;
    private $sessionStrategy;
    private $container;

    /**
     * @var AbstractRememberMeServices[]
     */
    private $rememberMeServices;

    public function __construct(SecurityContextInterface $context, UserCheckerInterface $userChecker,
                                SessionAuthenticationStrategyInterface $sessionStrategy,
                                ContainerInterface $container,
                                $rememberMeServices)
    {
        $this->securityContext = $context;
        $this->userChecker = $userChecker;
        $this->sessionStrategy = $sessionStrategy;
        $this->container = $container;
        $this->rememberMeServices = $rememberMeServices;
    }

    final public function loginUser($firewallName, UserInterface $user, Response $response = null)
    {
        $this->userChecker->checkPostAuth($user);

        $token = $this->createToken($firewallName, $user);

        if ($this->container->isScopeActive('request')) {
            $this->sessionStrategy->onAuthentication($this->container->get('request'), $token);

            if (null !== $response && isset($this->rememberMeServices[$firewallName])) {
                $rememberMeServices = $this->rememberMeServices[$firewallName];
                if ($rememberMeServices instanceof RememberMeServicesInterface) {
                    $rememberMeServices->loginSuccess($this->container->get('request'), $response, $token);
                }
            }
        }

        $this->securityContext->setToken($token);
    }

    protected function createToken($firewall, UserInterface $user)
    {
        return new UsernamePasswordToken($user, null, $firewall, $user->getRoles());
    }
}
