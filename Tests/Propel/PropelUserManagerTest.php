<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Propel;

class PropelUserManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $userManager;

    public function testFindUserByUsername()
    {
        $this->userManager->expects($this->once())
            ->method('findUserBy')
            ->with($this->equalTo(array('usernameCanonical' => 'jack')));
        $this->userManager->expects($this->once())
            ->method('canonicalizeUsername')
            ->with($this->equalTo('jack'))
            ->will($this->returnValue('jack'));

        $this->userManager->findUserByUsername('jack');
    }

    public function testFindUserByUsernameLowercasesTheUsername()
    {
        $this->userManager->expects($this->once())
            ->method('findUserBy')
            ->with($this->equalTo(array('usernameCanonical' => 'jack')));
        $this->userManager->expects($this->once())
            ->method('canonicalizeUsername')
            ->with($this->equalTo('JaCk'))
            ->will($this->returnValue('jack'));

        $this->userManager->findUserByUsername('JaCk');
    }

    public function testFindUserByEmail()
    {
        $this->userManager->expects($this->once())
            ->method('findUserBy')
            ->with($this->equalTo(array('emailCanonical' => 'jack@email.org')));
        $this->userManager->expects($this->once())
            ->method('canonicalizeEmail')
            ->with($this->equalTo('jack@email.org'))
            ->will($this->returnValue('jack@email.org'));

        $this->userManager->findUserByEmail('jack@email.org');
    }

    public function testFindUserByEmailLowercasesTheEmail()
    {
        $this->userManager->expects($this->once())
            ->method('findUserBy')
            ->with($this->equalTo(array('emailCanonical' => 'jack@email.org')));
        $this->userManager->expects($this->once())
            ->method('canonicalizeEmail')
            ->with($this->equalTo('JaCk@EmAiL.oRg'))
            ->will($this->returnValue('jack@email.org'));

        $this->userManager->findUserByEmail('JaCk@EmAiL.oRg');
    }

    public function testFindUserByUsernameOrEmailWithUsername()
    {
        $this->userManager->expects($this->once())
            ->method('findUserBy')
            ->with($this->equalTo(array('usernameCanonical' => 'jack')));
        $this->userManager->expects($this->once())
            ->method('canonicalizeUsername')
            ->with($this->equalTo('JaCk'))
            ->will($this->returnValue('jack'));

        $this->userManager->findUserByUsernameOrEmail('JaCk');
    }

    public function testFindUserByUsernameOrEmailWithEmail()
    {
        $this->userManager->expects($this->once())
            ->method('findUserBy')
            ->with($this->equalTo(array('emailCanonical' => 'jack@email.org')));
        $this->userManager->expects($this->once())
            ->method('canonicalizeEmail')
            ->with($this->equalTo('JaCk@EmAiL.oRg'))
            ->will($this->returnValue('jack@email.org'));

        $this->userManager->findUserByUsernameOrEmail('JaCk@EmAiL.oRg');
    }

    public function testLoadUserByUsernameWithExistingUser()
    {
        $userMock = $this->getMock('FOS\UserBundle\Propel\User', array(), array('sha1'));

        $manager = $this->getMockBuilder('FOS\UserBundle\Propel\UserManager')
            ->disableOriginalConstructor()
            ->setMethods(array('findUserByUsername'))
            ->getMock();

        $manager->expects($this->once())
            ->method('findUserByUsername')
            ->with($this->equalTo('jack'))
            ->will($this->returnValue($userMock));

        $manager->loadUserByUsername('jack');
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testLoadUserByUsernameWithMissingUser()
    {
        $manager = $this->getMockBuilder('FOS\UserBundle\Propel\UserManager')
            ->disableOriginalConstructor()
            ->setMethods(array('findUserByUsername'))
            ->getMock();

        $manager->expects($this->once())
            ->method('findUserByUsername')
            ->with($this->equalTo('jack'))
            ->will($this->returnValue(null));

        $manager->loadUserByUsername('jack');
    }

    protected function setUp()
    {
        if (file_exists($file = __DIR__.'/../../../../../propel/runtime/lib/Propel.php')) {
            require_once $file;
        }

        if (!class_exists('\Propel')) {
            $this->markTestSkipped('Propel not installed');
        }

        $this->userManager = $this->getManagerMock();
    }

    protected function tearDown()
    {
        unset($this->userManager);
    }

    protected function getManagerMock()
    {
        return $this->getMockBuilder('FOS\UserBundle\Propel\UserManager')
            ->disableOriginalConstructor()
            ->setMethods(array('findUserBy', 'canonicalizeUsername', 'canonicalizeEmail'))
            ->getMock();
    }
}
