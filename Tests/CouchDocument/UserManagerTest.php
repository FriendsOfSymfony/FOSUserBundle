<?php

namespace FOS\UserBundle\Tests\CouchDocument;

use FOS\UserBundle\CouchDocument\UserManager;
use Doctrine\ODM\CouchDB\Mapping\ClassMetadata;

class UserManagerTest extends \PHPUnit_Framework_TestCase
{
    private $userManager;
    private $dm;
    private $repository;

    const USERTYPE = 'FOS\UserBundle\Tests\CouchDocument\DummyUser';

    public function setUp()
    {
        if (!class_exists('Doctrine\ODM\CouchDB\Version')) {
            $this->markTestSkipped('Doctrine CouchDB has to be installed for this test to run.');
        }

        $c = $this->getMock('FOS\UserBundle\Util\CanonicalizerInterface');
        $ef = $this->getMock('Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface');

        $class = new ClassMetadata(self::USERTYPE);
        $class->mapField(array('fieldName' => 'username'));

        $this->dm = $this->getMock('Doctrine\ODM\CouchDB\DocumentManager', array('getRepository', 'persist', 'remove', 'flush', 'getClassMetadata'), array(), '', false);
        $this->repository = $this->getMock('Doctrine\ODM\CouchDB\DocumentRepository', array('findBy', 'findAll'), array(), '', false);
        $this->dm->expects($this->any())
                 ->method('getRepository')
                 ->with($this->equalTo(self::USERTYPE))
                 ->will($this->returnValue($this->repository));
        $this->dm->expects($this->any())
                 ->method('getClassMetadata')
                 ->with($this->equalTo(self::USERTYPE))
                 ->will($this->returnValue($class));
        $this->userManager = new UserManager($ef, $c, $c, $this->dm, self::USERTYPE);
    }

    public function testDeleteUser()
    {
        $user = new DummyUser();
        $this->dm->expects($this->once())->method('remove')->with($this->equalTo($user));
        $this->dm->expects($this->once())->method('flush');

        $this->userManager->deleteUser($user);
    }

    public function testGetClass()
    {
        $this->assertEquals(self::USERTYPE, $this->userManager->getClass());
    }

    public function testFindUserBy()
    {
        $crit = array("foo" => "bar");
        $this->repository->expects($this->once())->method('findBy')->with($this->equalTo($crit))->will($this->returnValue(array()));

        $this->userManager->findUserBy($crit);
    }

    public function testFindUsers()
    {
        $this->repository->expects($this->once())->method('findAll')->will($this->returnValue(array()));

        $this->userManager->findUsers();
    }

    public function testUpdateUser()
    {
        $user = new DummyUser();
        $this->dm->expects($this->once())->method('persist')->with($this->equalTo($user));
        $this->dm->expects($this->once())->method('flush');

        $this->userManager->updateUser($user);
    }
}

class DummyUser extends \FOS\UserBundle\Document\User
{

}
