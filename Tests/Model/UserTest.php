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

use FOS\UserBundle\Model\User;

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

    public function testIsPasswordRequestNonExpired()
    {
        $user = $this->getUser();
        $passwordRequestedAt = new \DateTime('-10 seconds');

        $user->setPasswordRequestedAt($passwordRequestedAt);

        $this->assertSame($passwordRequestedAt, $user->getPasswordRequestedAt());
        $this->assertTrue($user->isPasswordRequestNonExpired(15));
        $this->assertFalse($user->isPasswordRequestNonExpired(5));
    }

    public function testTrueHasRole()
    {
        $user = $this->getUser();
        $defaultrole = User::ROLE_DEFAULT;
        $newrole = 'ROLE_X';
        $this->assertTrue($user->hasRole($defaultrole));
        $user->addRole($defaultrole);
        $this->assertTrue($user->hasRole($defaultrole));
        $user->addRole($newrole);
        $this->assertTrue($user->hasRole($newrole));
    }

    public function testFalseHasRole()
    {
        $user = $this->getUser();
        $newrole = 'ROLE_X';
        $this->assertFalse($user->hasRole($newrole));
        $user->addRole($newrole);
        $this->assertTrue($user->hasRole($newrole));
    }

    public function testForEqualUsers()
    {
        $user1 = $this->getMockBuilder('FOS\UserBundle\Model\User')->setMethods(array('getSalt'))->getMock();
        $user2 = $this->getMockBuilder('FOS\UserBundle\Model\User')->setMethods(array('getSalt'))->getMock();
        $user3 = $this->getMockBuilder('FOS\UserBundle\Model\User')->setMethods(array('getSalt'))->getMock();
        
        $salt1 = $salt3 = 'xxxx';
        $salt2 = 'yyyy';

        $user2->expects($this->once())
            ->method('getSalt')
            ->will($this->returnValue($salt2));

        $user1->expects($this->any())
            ->method('getSalt')
            ->will($this->returnValue($salt1));

        $user3->expects($this->once())
            ->method('getSalt')
            ->will($this->returnValue($salt3));

        $this->assertFalse($user1->equals($user2));
        $this->assertTrue($user1->equals($user1));
        $this->assertTrue($user1->equals($user3));
    }

    protected function getUser()
    {
        return $this->getMockForAbstractClass('FOS\UserBundle\Model\User');
    }
}
