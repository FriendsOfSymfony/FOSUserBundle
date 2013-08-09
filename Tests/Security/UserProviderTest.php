<?php

namespace FOS\UserBundle\Tests\Security;

use FOS\UserBundle\Security\UserProvider;

class UserProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $userManager;

    /**
     * @var UserProvider
     */
    private $userProvider;

    protected function setUp()
    {
        $this->userManager = $this->getMock('FOS\UserBundle\Model\UserManagerInterface');
        $this->userProvider = new UserProvider($this->userManager);
    }

    public function testLoadUserByUsername()
    {
        $user = $this->getMock('FOS\UserBundle\Model\UserInterface');
        $this->userManager->expects($this->once())
            ->method('findUserByUsername')
            ->with('foobar')
            ->will($this->returnValue($user));

        $this->assertSame($user, $this->userProvider->loadUserByUsername('foobar'));
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testLoadUserByInvalidUsername()
    {
        $this->userManager->expects($this->once())
            ->method('findUserByUsername')
            ->with('foobar')
            ->will($this->returnValue(null));

        $this->userProvider->loadUserByUsername('foobar');
    }

    public function testRefreshUserBy()
    {
        $user = $this->getMockBuilder('FOS\UserBundle\Model\User')
                    ->setMethods(array('getId'))
                    ->getMock();

        $user->expects($this->once())
            ->method('getId')
            ->will($this->returnValue('123'));

        $refreshedUser = $this->getMock('FOS\UserBundle\Model\UserInterface');
        $this->userManager->expects($this->once())
            ->method('findUserBy')
            ->with(array('id' => '123'))
            ->will($this->returnValue($refreshedUser));

        $this->userManager->expects($this->atLeastOnce())
            ->method('getClass')
            ->will($this->returnValue(get_class($user)));

        $this->assertSame($refreshedUser, $this->userProvider->refreshUser($user));
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testRefreshDeleted()
    {
        $user = $this->getMockForAbstractClass('FOS\UserBundle\Model\User');
        $this->userManager->expects($this->once())
            ->method('findUserBy')
            ->will($this->returnValue(null));

        $this->userManager->expects($this->atLeastOnce())
            ->method('getClass')
            ->will($this->returnValue(get_class($user)));

        $this->userProvider->refreshUser($user);
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\UnsupportedUserException
     */
    public function testRefreshInvalidUser()
    {
        $user = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
        $this->userManager->expects($this->any())
            ->method('getClass')
            ->will($this->returnValue(get_class($user)));

        $this->userProvider->refreshUser($user);
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\UnsupportedUserException
     */
    public function testRefreshInvalidUserClass()
    {
        $user = $this->getMock('FOS\UserBundle\Model\User');
        $providedUser = $this->getMock('FOS\UserBundle\Tests\TestUser');

        $this->userManager->expects($this->atLeastOnce())
            ->method('getClass')
            ->will($this->returnValue(get_class($user)));

        $this->userProvider->refreshUser($providedUser);
    }
}
