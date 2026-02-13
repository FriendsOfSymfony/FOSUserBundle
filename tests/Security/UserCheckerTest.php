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

use FOS\UserBundle\Model\User;
use FOS\UserBundle\Security\UserChecker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\DisabledException;

class UserCheckerTest extends TestCase
{
    public function testCheckPreAuthFailsIsEnabled()
    {
        $this->expectExceptionMessage('User account is disabled.');
        $this->expectException(DisabledException::class);

        $userMock = $this->getUser(false);
        $checker = new UserChecker();
        $checker->checkPreAuth($userMock);
    }

    public function testCheckPreAuthSuccess()
    {
        $userMock = $this->getUser(true);
        $checker = new UserChecker();

        $checker->checkPreAuth($userMock);
        $this->expectNotToPerformAssertions();
    }

    public function testCheckPostAuthSuccess()
    {
        $userMock = $this->getUser(true);
        $checker = new UserChecker();

        $checker->checkPostAuth($userMock);
        $this->expectNotToPerformAssertions();
    }

    private function getUser(bool $isEnabled): User
    {
        $userMock = $this->getMockBuilder('FOS\UserBundle\Model\User')->getMock();
        $userMock
            ->method('isEnabled')
            ->willReturn($isEnabled);

        return $userMock;
    }
}
