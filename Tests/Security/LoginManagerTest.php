<?php

namespace FOS\UserBundle\Tests\Security;

use FOS\UserBundle\Security\LoginManager;
use Symfony\Component\HttpFoundation\Response;

class LoginManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testLogInUserWithRequestStack()
    {
        if (!class_exists('Symfony\Component\HttpFoundation\RequestStack')) {
            $this->markTestSkipped('The RequestStack is required to run this test.');
        }

        $loginManager = $this->createLoginManager('main');
        $loginManager->logInUser('main', $this->mockUser());
    }

    public function testLogInUserWithRememberMeAndRequestStack()
    {
        if (!class_exists('Symfony\Component\HttpFoundation\RequestStack')) {
            $this->markTestSkipped('The RequestStack is required to run this test.');
        }

        $response = $this->getMock('Symfony\Component\HttpFoundation\Response');

        $loginManager = $this->createLoginManager('main', $response);
        $loginManager->logInUser('main', $this->mockUser(), $response);
    }

    /**
     * @group legacy
     */
    public function testLogInUserWithoutRequestStack()
    {
        if (!method_exists('Symfony\Component\DependencyInjection\ContainerInterface', 'isScopeActive')) {
            $this->markTestSkipped('Legacy test. Container scopes are not supported any more.');
        }

        $loginManager = $this->createLoginManager('main', null, false);
        $loginManager->logInUser('main', $this->mockUser());
    }

    /**
     * @group legacy
     */
    public function testLogInUserWithRememberMeAndWithoutRequestStack()
    {
        if (!method_exists('Symfony\Component\DependencyInjection\ContainerInterface', 'isScopeActive')) {
            $this->markTestSkipped('Legacy test. Container scopes are not supported any more.');
        }

        $response = $this->getMock('Symfony\Component\HttpFoundation\Response');

        $loginManager = $this->createLoginManager('main', $response, false);
        $loginManager->logInUser('main', $this->mockUser(), $response);
    }

    private function createLoginManager($firewallName, Response $response = null, $withRequestStack = true)
    {
        if (interface_exists('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface')) {
            $tokenStorage = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface');
        } else {
            $tokenStorage = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        }

        $tokenStorage
            ->expects($this->once())
            ->method('setToken')
            ->with($this->isInstanceOf('Symfony\Component\Security\Core\Authentication\Token\TokenInterface'));

        $userChecker = $this->getMock('Symfony\Component\Security\Core\User\UserCheckerInterface');
        $userChecker
            ->expects($this->once())
            ->method('checkPostAuth')
            ->with($this->isInstanceOf('FOS\UserBundle\Model\UserInterface'));

        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');

        $sessionStrategy = $this->getMock('Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface');
        $sessionStrategy
            ->expects($this->once())
            ->method('onAuthentication')
            ->with($request, $this->isInstanceOf('Symfony\Component\Security\Core\Authentication\Token\TokenInterface'));

        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $getMap = $hasMap = array();

        if (true === $withRequestStack) {
            $requestStack = $this->getMock('Symfony\Component\HttpFoundation\RequestStack');
            $requestStack
                ->expects($this->once())
                ->method('getCurrentRequest')
                ->will($this->returnValue($request));

            $hasMap[] = array('request_stack', true);
            $getMap[] = array('request_stack', 1, $requestStack);
        } else {
            $container
                ->expects($this->once())
                ->method('isScopeActive')
                ->with('request')
                ->will($this->returnValue(true));
            $getMap[] = array('request', 1, $request);
        }

        if (null !== $response) {
            $rememberMe = $this->getMock('Symfony\Component\Security\Http\RememberMe\RememberMeServicesInterface');
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

    private function mockUser()
    {
        $user = $this->getMock('FOS\UserBundle\Model\UserInterface');
        $user
            ->expects($this->once())
            ->method('getRoles')
            ->will($this->returnValue(array('ROLE_USER')));

        return $user;
    }
}
