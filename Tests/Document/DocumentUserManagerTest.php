<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Document;

class DocumentUserManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $userManager;

    public function testFindUserByUsername()
    {
        $this->userManager->expects($this->once())
            ->method('findUserBy')
            ->with($this->equalTo(array('usernameCanonical' => 'jack')));

        $this->userManager->findUserByUsername('jack');
    }

    public function testFindUserByUsernameLowercasesTheUsername()
    {
        $this->userManager->expects($this->once())
            ->method('findUserBy')
            ->with($this->equalTo(array('usernameCanonical' => 'jack')));

        $this->userManager->findUserByUsername('JaCk');
    }

    public function testFindUserByEmail()
    {
        $this->userManager->expects($this->once())
            ->method('findUserBy')
            ->with($this->equalTo(array('email' => 'jack@email.org')));

        $this->userManager->findUserByEmail('jack@email.org');
    }

    public function testFindUserByEmailLowercasesTheEmail()
    {
        $this->userManager->expects($this->once())
            ->method('findUserBy')
            ->with($this->equalTo(array('email' => 'jack@email.org')));

        $this->userManager->findUserByEmail('JaCk@EmAiL.oRg');
    }

    public function testFindUserByUsernameOrEmailWithUsername()
    {
        $this->userManager->expects($this->once())
            ->method('findUserBy')
            ->with($this->equalTo(array('usernameCanonical' => 'jack')));

        $this->userManager->findUserByUsernameOrEmail('JaCk');
    }

    public function testFindUserByUsernameOrEmailWithEmail()
    {
        $this->userManager->expects($this->once())
            ->method('findUserBy')
            ->with($this->equalTo(array('email' => 'jack@email.org')));

        $this->userManager->findUserByUsernameOrEmail('JaCk@EmAiL.oRg');
    }

    public function testLoadUserByUsernameWithExistingUser()
    {
        $userMock = $this->getMock('FOS\UserBundle\Document\User', array(), array('sha1'));

        $this->userManager->expects($this->once())
            ->method('findUserBy')
            ->with($this->equalTo(array('usernameCanonical' => 'jack')))
            ->will($this->returnValue($userMock));

        $this->userManager->loadUserByUsername('jack');
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testLoadUserByUsernameWithMissingUser()
    {
        $this->userManager->expects($this->once())
            ->method('findUserBy')
            ->with($this->equalTo(array('usernameCanonical' => 'jack')))
            ->will($this->returnValue(null));

        $this->userManager->loadUserByUsername('jack');
    }

    protected function setUp()
    {
        if (!class_exists('\Doctrine\ODM\MongoDB\DocumentManager')) {
            $this->markTestSkipped('No ODM installed');
        }

        $this->userManager = $this->getManagerMock();
    }

    protected function tearDown()
    {
        unset($this->userManager);
    }

    protected function getManagerMock()
    {
        $methods = array('findUserBy');
        $userManager = $this->getMock('FOS\UserBundle\Document\UserManager', $methods, array(), '', false);

        return $userManager;
    }
}
