<?php

namespace FOS\UserBundle\Tests\Doctrine;

use FOS\UserBundle\Doctrine\UserManager;
use FOS\UserBundle\Model\User;

class UserManagerTest extends \PHPUnit_Framework_TestCase
{
    const USER_CLASS = 'FOS\UserBundle\Tests\Doctrine\DummyUser';

    /** @var UserManager */
    protected $userManager;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $om;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $repository;

    public function setUp()
    {
        if (!interface_exists('Doctrine\Common\Persistence\ObjectManager')) {
            $this->markTestSkipped('Doctrine Common has to be installed for this test to run.');
        }

        $c = $this->getMock('FOS\UserBundle\Util\CanonicalizerInterface');
        $ef = $this->getMock('Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface');
        $class = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
        $this->om = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $this->repository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');

        $this->om->expects($this->any())
            ->method('getRepository')
            ->with($this->equalTo(static::USER_CLASS))
            ->will($this->returnValue($this->repository));
        $this->om->expects($this->any())
            ->method('getClassMetadata')
            ->with($this->equalTo(static::USER_CLASS))
            ->will($this->returnValue($class));
        $class->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(static::USER_CLASS));

        $this->userManager = $this->createUserManager($ef, $c, $this->om, static::USER_CLASS);
    }

    public function testDeleteUser()
    {
        $user = $this->getUser();
        $this->om->expects($this->once())->method('remove')->with($this->equalTo($user));
        $this->om->expects($this->once())->method('flush');

        $this->userManager->deleteUser($user);
    }

    public function testGetClass()
    {
        $this->assertEquals(static::USER_CLASS, $this->userManager->getClass());
    }

    public function testFindUserBy()
    {
        $crit = array("foo" => "bar");
        $this->repository->expects($this->once())->method('findOneBy')->with($this->equalTo($crit))->will($this->returnValue(array()));

        $this->userManager->findUserBy($crit);
    }

    public function testFindUsers()
    {
        $this->repository->expects($this->once())->method('findAll')->will($this->returnValue(array()));

        $this->userManager->findUsers();
    }

    public function testUpdateUser()
    {
        $user = $this->getUser();
        $this->om->expects($this->once())->method('persist')->with($this->equalTo($user));
        $this->om->expects($this->once())->method('flush');

        $this->userManager->updateUser($user);
    }

    protected function createUserManager($encoderFactory, $canonicalizer, $objectManager, $userClass)
    {
        return new UserManager($encoderFactory, $canonicalizer, $canonicalizer, $objectManager, $userClass);
    }

    protected function getUser()
    {
        $userClass = static::USER_CLASS;

        return new $userClass();
    }
}

class DummyUser extends User
{

}
