<?php

namespace Bundle\FOS\UserBundle\Tests\Model;

class UserTest extends \PHPUnit_Framework_TestCase
{
    public function testUsername()
    {
        $user = $this->getUser();
        $this->assertNull($user->getUsername());

        $user->setUsername('tony');
        $this->assertEquals('tony', $user->getUsername());
        $this->assertEquals('tony', $user->getDisplayName());
    }

    public function testEmail()
    {
        $user = $this->getUser();
        $this->assertNull($user->getEmail());

        $user->setEmail('tony@mail.org');
        $this->assertEquals('tony@mail.org', $user->getEmail());
    }

    protected function getUser()
    {
        return $this->getMockForAbstractClass('Bundle\FOS\UserBundle\Model\User');
    }
}
