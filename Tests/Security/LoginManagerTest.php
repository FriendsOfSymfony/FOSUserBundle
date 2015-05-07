<?php

namespace FOS\UserBundle\Tests\Security;

use FOS\UserBundle\Security\LoginManager;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class LoginManagerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var LoginManager
     */
    private $loginManager;

    /**
     * @var UserCheckerInterface
     */
    private $userChecker;

    /**
     * @var SessionAuthenticationStrategyInterface
     */
    private $sessionStrategy;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    protected function setUp()
    {
        $this->tokenStorage = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface');
        $this->userChecker = $this->getMock('Symfony\Component\Security\Core\User\UserCheckerInterface');
        $this->sessionStrategy = $this->getMock('Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface');
        $this->container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->loginManager = new LoginManager($this->tokenStorage, $this->userChecker, $this->sessionStrategy, $this->container);
    }

    public function testCallCheckPreAuth()
    {
        $this->userChecker
            ->expects($this->once())
            ->method('checkPreAuth');

        $user = $this->getMock('FOS\UserBundle\Model\UserInterface');

        $this->loginManager->loginUser('foo', $user);
    }

}
