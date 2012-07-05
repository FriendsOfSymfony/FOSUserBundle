<?php

namespace FOS\UserBundle\Tests\Security;

class UserProviderTest extends \PHPUnit_Framework_TestCase
{
    private $userManager;
    private $userProvider;

    protected function setUp()
    {
        $this->userManager = $this->getMockUserManager();
        $this->userProvider = $this->getUserProvider(array(
            $this->userManager,
        ));
    }

    public function testLoadUser()
    {
        // ? no idea
    }

    private function getMockUserManager()
    {
        return $this->getMock('FOS\UserBundle\Model\UserManager');
    }

    private function getUser()
    {
        return $this->getMockBuilder('FOS\UserBundle\Model\User')
            ->getMockForAbstractClass();
    }

    private function getUserProvider(array $args)
    {
        return $this->getMockBuilder('FOS\UserBundle\Security\UserProvider')
            ->setConstructorArgs($args)
            ;
    }
}
