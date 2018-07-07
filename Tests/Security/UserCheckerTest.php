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

use FOS\UserBundle\Security\UserChecker;
use PHPUnit\Framework\TestCase;

use Symfony\Component\Security\Core\Exception\CredentialsExpiredException;
use Symfony\Component\Security\Core\Exception\LockedException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;

class UserCheckerTest extends TestCase
{
    public function testCheckPreAuthFailsLockedOut()
    {
        $userMock = $this->getUser(false, false, false, false);
        $this->expectException(LockedException::class);
        $this->expectExceptionMessage('User account is locked.');

        $checker = new UserChecker();
        $checker->checkPreAuth($userMock);
    }

    public function testCheckPreAuthFailsIsEnabled()
    {
        $userMock = $this->getUser(true, false, false, false);
        $this->expectException(DisabledException::class);
        $this->expectExceptionMessage('User account is disabled.');

        $checker = new UserChecker();
        $checker->checkPreAuth($userMock);
    }

    public function testCheckPreAuthFailsIsAccountNonExpired()
    {
        $userMock = $this->getUser(true, true, false, false);
        $this->expectException(AccountExpiredException::class);
        $this->expectExceptionMessage('User account has expired.');

        $checker = new UserChecker();
        $checker->checkPreAuth($userMock);
    }

    public function testCheckPreAuthSuccess()
    {
        $userMock = $this->getUser(true, true, true, false);
        $checker = new UserChecker();
        try {
            $this->assertNull($checker->checkPreAuth($userMock));
        } catch (\Exception $ex) {
            $this->fail();
        }
    }

    public function testCheckPostAuthFailsIsCredentialsNonExpired()
    {
        $userMock = $this->getUser(true, true, true, false);
        $this->expectException(CredentialsExpiredException::class);
        $this->expectExceptionMessage('User credentials have expired.');

        $checker = new UserChecker();
        $checker->checkPostAuth($userMock);
    }

    public function testCheckPostAuthSuccess()
    {
        $userMock = $this->getUser(true, true, true, true);
        $checker = new UserChecker();
        try {
            $this->assertNull($checker->checkPostAuth($userMock));
        } catch (\Exception $ex) {
            $this->fail();
        }
    }

    private function getUser($isAccountNonLocked, $isEnabled, $isAccountNonExpired, $isCredentialsNonExpired)
    {
        $userMock = $this->createMock('FOS\UserBundle\Model\User');

        $userMock
            ->method('isAccountNonLocked')
            ->willReturn($isAccountNonLocked);
        $userMock
            ->method('isEnabled')
            ->willReturn($isEnabled);
        $userMock
            ->method('isAccountNonExpired')
            ->willReturn($isAccountNonExpired);
        $userMock
            ->method('isCredentialsNonExpired')
            ->willReturn($isCredentialsNonExpired);

        return $userMock;
    }
}
