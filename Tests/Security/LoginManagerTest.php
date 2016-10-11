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

use FOS\UserBundle\Security\LoginManager;
use Symfony\Component\HttpFoundation\Response;

class LoginManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testLogInUserWithRequestStack()
    {
        $loginManager = $this->createLoginManager('main');
        $loginManager->logInUser('main', $this->mockUser());
    }

    public function testLogInUserWithRememberMeAndRequestStack()
    {
        $response = $this->getMockBuilder('Symfony\Component\HttpFoundation\Response')->getMock();

        $loginManager = $this->createLoginManager('main', $response);
        $loginManager->logInUser('main', $this->mockUser(), $response);
    }

    /**
     * @param string        $firewallName
     * @param Response|null $response
     *
     * @return LoginManager
     */
    private function createLoginManager($firewallName, Response $response = null)
    {
        $tokenStorage = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface')->getMock();

        $tokenStorage
            ->expects($this->once())
            ->method('setToken')
            ->with($this->isInstanceOf('Symfony\Component\Security\Core\Authentication\Token\TokenInterface'));

        $userChecker = $this->getMockBuilder('Symfony\Component\Security\Core\User\UserCheckerInterface')->getMock();
        $userChecker
            ->expects($this->once())
            ->method('checkPreAuth')
            ->with($this->isInstanceOf('FOS\UserBundle\Model\UserInterface'));

        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')->getMock();

        $sessionStrategy = $this->getMockBuilder('Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface')->getMock();
        $sessionStrategy
            ->expects($this->once())
            ->method('onAuthentication')
            ->with($request, $this->isInstanceOf('Symfony\Component\Security\Core\Authentication\Token\TokenInterface'));

        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')->getMock();
        $getMap = $hasMap = array();

        $requestStack = $this->getMockBuilder('Symfony\Component\HttpFoundation\RequestStack')->getMock();
        $requestStack
            ->expects($this->once())
            ->method('getCurrentRequest')
            ->will($this->returnValue($request));

        $hasMap[] = array('request_stack', true);
        $getMap[] = array('request_stack', 1, $requestStack);

        if (null !== $response) {
            $rememberMe = $this->getMockBuilder('Symfony\Component\Security\Http\RememberMe\RememberMeServicesInterface')->getMock();
            $rememberMe
                ->expects($this->once())
                ->method('loginSuccess')
                ->with($request, $response, $this->isInstanceOf('Symfony\Component\Security\Core\Authentication\Token\TokenInterface'));

            $hasMap[] = array('security.authentication.rememberme.services.persistent.'.$firewallName, true);
            $getMap[] = array('security.authentication.rememberme.services.persistent.'.$firewallName, 1, $rememberMe);
        }

        $container
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap($getMap));

        $container
            ->expects($this->any())
            ->method('has')
            ->will($this->returnValueMap($hasMap));

        return new LoginManager($tokenStorage, $userChecker, $sessionStrategy, $container);
    }

    /**
     * @return mixed
     */
    private function mockUser()
    {
        $user = $this->getMockBuilder('FOS\UserBundle\Model\UserInterface')->getMock();
        $user
            ->expects($this->once())
            ->method('getRoles')
            ->will($this->returnValue(array('ROLE_USER')));

        return $user;
    }
}
