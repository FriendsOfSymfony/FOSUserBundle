<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Tests\Security;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Security\LoginManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\RememberMe\RememberMeHandlerInterface;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;

class LoginManagerTest extends TestCase
{
    public function testLogInUserWithRequestStack()
    {
        $loginManager = $this->createLoginManager();
        $loginManager->logInUser('main', $this->mockUser());
    }

    public function testLogInUserWithRememberMeHandler()
    {
        $response = new Response();
        $user = $this->mockUser();

        $rememberMeHandler = $this->createMock(RememberMeHandlerInterface::class);
        $rememberMeHandler->expects($this->once())
            ->method('createRememberMeCookie')
            ->with($user);

        $loginManager = $this->createLoginManager($rememberMeHandler);
        $loginManager->logInUser('main', $user, $response);
    }

    private function createLoginManager(?RememberMeHandlerInterface $rememberMeHandler = null): LoginManager
    {
        $tokenStorage = $this->getMockBuilder(TokenStorageInterface::class)->getMock();
        $tokenStorage
            ->expects($this->once())
            ->method('setToken')
            ->with($this->isInstanceOf(TokenInterface::class));

        $userChecker = $this->getMockBuilder('Symfony\Component\Security\Core\User\UserCheckerInterface')->getMock();
        $userChecker
            ->expects($this->once())
            ->method('checkPreAuth')
            ->with($this->isInstanceOf(UserInterface::class));

        $request = new Request();

        $sessionStrategy = $this->getMockBuilder(SessionAuthenticationStrategyInterface::class)->getMock();
        $sessionStrategy
            ->expects($this->once())
            ->method('onAuthentication')
            ->with($request, $this->isInstanceOf(TokenInterface::class));

        $requestStack = new RequestStack();
        $requestStack->push($request);

        return new LoginManager($tokenStorage, $userChecker, $sessionStrategy, $requestStack, $rememberMeHandler);
    }

    private function mockUser(): UserInterface
    {
        $user = $this->getMockBuilder(UserInterface::class)->getMock();
        $user
            ->expects($this->once())
            ->method('getRoles')
            ->will($this->returnValue(['ROLE_USER']));

        return $user;
    }
}
