<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Tests\Util;

use FOS\UserBundle\Tests\TestUser;
use FOS\UserBundle\Util\PasswordUpdater;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class PasswordUpdaterTest extends TestCase
{
    /**
     * @var PasswordUpdater
     */
    private $updater;
    private $hasherFactory;

    protected function setUp(): void
    {
        $this->hasherFactory = $this->getMockBuilder(PasswordHasherFactoryInterface::class)->getMock();

        $this->updater = new PasswordUpdater($this->hasherFactory);
    }

    public function testUpdatePassword()
    {
        $hasher = $this->getMockPasswordHasher();
        $user = new TestUser();
        $user->setPlainPassword('password');

        $this->hasherFactory->expects($this->once())
            ->method('getPasswordHasher')
            ->with(get_class($user))
            ->will($this->returnValue($hasher));

        $hasher->expects($this->once())
            ->method('hash')
            ->with('password')
            ->will($this->returnValue('encodedPassword'));

        $this->updater->hashPassword($user);
        $this->assertSame('encodedPassword', $user->getPassword(), '->updatePassword() sets encoded password');
        $this->assertNull($user->getPlainPassword(), '->updatePassword() erases credentials');
    }

    public function testDoesNotUpdateWithoutPlainPassword()
    {
        $user = new TestUser();
        $user->setPassword('hash');

        $user->setPlainPassword('');

        $this->updater->hashPassword($user);
        $this->assertSame('hash', $user->getPassword());
    }

    private function getMockPasswordHasher()
    {
        return $this->getMockBuilder(PasswordHasherInterface::class)->disableOriginalConstructor()->getMock();
    }
}
