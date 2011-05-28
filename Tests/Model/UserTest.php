<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Tests\Model;

class UserTest extends \PHPUnit_Framework_TestCase
{
    public function testUsername()
    {
        $user = $this->getUser();
        $this->assertNull($user->getUsername());

        $user->setUsername('tony');
        $this->assertEquals('tony', $user->getUsername());
    }

    public function testEmail()
    {
        $user = $this->getUser();
        $this->assertNull($user->getEmail());

        $user->setEmail('tony@mail.org');
        $this->assertEquals('tony@mail.org', $user->getEmail());
    }

    /**
     * @covers FOS\UserBundle\Model\User::getPasswordRequestedAt
     * @covers FOS\UserBundle\Model\User::setPasswordRequestedAt
     * @covers FOS\UserBundle\Model\User::isPasswordRequestNonExpired
     */
    public function testIsPasswordRequestNonExpired()
    {
        $user = $this->getUser();
        $passwordRequestedAt = new \DateTime('-10 seconds');

        $user->setPasswordRequestedAt($passwordRequestedAt);

        $this->assertSame($passwordRequestedAt, $user->getPasswordRequestedAt());
        $this->assertTrue($user->isPasswordRequestNonExpired(15));
        $this->assertFalse($user->isPasswordRequestNonExpired(5));
    }

    protected function getUser()
    {
        return $this->getMockForAbstractClass('FOS\UserBundle\Model\User');
    }
}
