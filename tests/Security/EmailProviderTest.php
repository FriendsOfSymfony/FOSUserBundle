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

use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Security\EmailProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class EmailProviderTest extends TestCase
{
    /**
     * @var UserManagerInterface&MockObject
     */
    private $userManager;

    /**
     * @var EmailProvider
     */
    private $userProvider;

    protected function setUp(): void
    {
        $this->userManager = $this->getMockBuilder('FOS\UserBundle\Model\UserManagerInterface')->getMock();
        $this->userProvider = new EmailProvider($this->userManager);
    }

    public function testLoadUserByUsername()
    {
        $user = $this->getMockBuilder('FOS\UserBundle\Model\UserInterface')->getMock();
        $this->userManager->expects($this->once())
            ->method('findUserByEmail')
            ->with('foobar')
            ->will($this->returnValue($user));

        $this->assertSame($user, $this->userProvider->loadUserByIdentifier('foobar'));
    }

    public function testLoadUserByInvalidUsername()
    {
        $this->expectException(UserNotFoundException::class);

        $this->userManager->expects($this->once())
            ->method('findUserByEmail')
            ->with('foobar')
            ->will($this->returnValue(null));

        $this->userProvider->loadUserByIdentifier('foobar');
    }

    public function testRefreshUserBy()
    {
        $user = $this->getMockBuilder('FOS\UserBundle\Model\User')
                    ->setMethods(['getId'])
                    ->getMock();

        $user->expects($this->once())
            ->method('getId')
            ->will($this->returnValue('123'));

        $refreshedUser = $this->getMockBuilder('FOS\UserBundle\Model\UserInterface')->getMock();
        $this->userManager->expects($this->once())
            ->method('findUserBy')
            ->with(['id' => '123'])
            ->will($this->returnValue($refreshedUser));

        $this->userManager->expects($this->atLeastOnce())
            ->method('getClass')
            ->will($this->returnValue(get_class($user)));

        $this->assertSame($refreshedUser, $this->userProvider->refreshUser($user));
    }

    public function testRefreshDeleted()
    {
        $this->expectException(UserNotFoundException::class);

        $user = $this->getMockForAbstractClass('FOS\UserBundle\Model\User');
        $this->userManager->expects($this->once())
            ->method('findUserBy')
            ->will($this->returnValue(null));

        $this->userManager->expects($this->atLeastOnce())
            ->method('getClass')
            ->will($this->returnValue(get_class($user)));

        $this->userProvider->refreshUser($user);
    }

    public function testRefreshInvalidUser()
    {
        $this->expectException(UnsupportedUserException::class);

        $user = $this->getMockBuilder('Symfony\Component\Security\Core\User\UserInterface')->getMock();
        $this->userManager->expects($this->any())
            ->method('getClass')
            ->will($this->returnValue(get_class($user)));

        $this->userProvider->refreshUser($user);
    }

    public function testRefreshInvalidUserClass()
    {
        $this->expectException(UnsupportedUserException::class);

        $user = $this->getMockBuilder('FOS\UserBundle\Model\User')->getMock();
        $providedUser = $this->getMockBuilder('FOS\UserBundle\Tests\TestUser')->getMock();

        $this->userManager->expects($this->atLeastOnce())
            ->method('getClass')
            ->will($this->returnValue(get_class($user)));

        $this->userProvider->refreshUser($providedUser);
    }
}
