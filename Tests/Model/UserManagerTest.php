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

class UserManagerTest extends \PHPUnit_Framework_TestCase
{
    private $manager;
    private $encoderFactory;
    private $usernameCanonicalizer;
    private $emailCanonicalizer;

    protected function setUp()
    {
        $this->encoderFactory        = $this->getMockEncoderFactory();
        $this->usernameCanonicalizer = $this->getMockCanonicalizer();
        $this->emailCanonicalizer    = $this->getMockCanonicalizer();

        $this->manager = $this->getUserManager(array(
            $this->encoderFactory,
            $this->usernameCanonicalizer,
            $this->emailCanonicalizer,
        ));
    }

    public function testUpdateCanonicalFields()
    {
        $user = $this->getUser();
        $user->setUsername('Username');
        $user->setEmail('User@Example.com');

        $this->usernameCanonicalizer->expects($this->once())
            ->method('canonicalize')
            ->with('Username')
            ->will($this->returnCallback('strtolower'));

        $this->emailCanonicalizer->expects($this->once())
            ->method('canonicalize')
            ->with('User@Example.com')
            ->will($this->returnCallback('strtolower'));

        $this->manager->updateCanonicalFields($user);
        $this->assertEquals('username', $user->getUsernameCanonical());
        $this->assertEquals('user@example.com', $user->getEmailCanonical());
    }

    public function testUpdatePassword()
    {
        $encoder = $this->getMockPasswordEncoder();
        $user = $this->getUser();
        $user->setPlainPassword('password');

        $this->encoderFactory->expects($this->once())
            ->method('getEncoder')
            ->will($this->returnValue($encoder));

        $encoder->expects($this->once())
            ->method('encodePassword')
            ->with('password', $user->getSalt())
            ->will($this->returnValue('encodedPassword'));

        $this->manager->updatePassword($user);
        $this->assertEquals('encodedPassword', $user->getPassword(), '->updatePassword() sets encoded password');
        $this->assertNull($user->getPlainPassword(), '->updatePassword() erases credentials');
    }

    public function testFindUserByUsername()
    {
        $this->manager->expects($this->once())
            ->method('findUserBy')
            ->with($this->equalTo(array('usernameCanonical' => 'jack')));
        $this->usernameCanonicalizer->expects($this->once())
            ->method('canonicalize')
            ->with('jack')
            ->will($this->returnValue('jack'));

        $this->manager->findUserByUsername('jack');
    }

    public function testFindUserByUsernameLowercasesTheUsername()
    {
        $this->manager->expects($this->once())
            ->method('findUserBy')
            ->with($this->equalTo(array('usernameCanonical' => 'jack')));
        $this->usernameCanonicalizer->expects($this->once())
            ->method('canonicalize')
            ->with('JaCk')
            ->will($this->returnValue('jack'));

        $this->manager->findUserByUsername('JaCk');
    }

    public function testFindUserByEmail()
    {
        $this->manager->expects($this->once())
            ->method('findUserBy')
            ->with($this->equalTo(array('emailCanonical' => 'jack@email.org')));
        $this->emailCanonicalizer->expects($this->once())
            ->method('canonicalize')
            ->with('jack@email.org')
            ->will($this->returnValue('jack@email.org'));

        $this->manager->findUserByEmail('jack@email.org');
    }

    public function testFindUserByEmailLowercasesTheEmail()
    {
        $this->manager->expects($this->once())
            ->method('findUserBy')
            ->with($this->equalTo(array('emailCanonical' => 'jack@email.org')));
        $this->emailCanonicalizer->expects($this->once())
            ->method('canonicalize')
            ->with('JaCk@EmAiL.oRg')
            ->will($this->returnValue('jack@email.org'));

        $this->manager->findUserByEmail('JaCk@EmAiL.oRg');
    }

    public function testFindUserByUsernameOrEmailWithUsername()
    {
        $this->manager->expects($this->once())
            ->method('findUserBy')
            ->with($this->equalTo(array('usernameCanonical' => 'jack')));
        $this->usernameCanonicalizer->expects($this->once())
            ->method('canonicalize')
            ->with('JaCk')
            ->will($this->returnValue('jack'));

        $this->manager->findUserByUsernameOrEmail('JaCk');
    }

    public function testFindUserByUsernameOrEmailWithEmail()
    {
        $this->manager->expects($this->once())
            ->method('findUserBy')
            ->with($this->equalTo(array('emailCanonical' => 'jack@email.org')));
        $this->emailCanonicalizer->expects($this->once())
            ->method('canonicalize')
            ->with('JaCk@EmAiL.oRg')
            ->will($this->returnValue('jack@email.org'));

        $this->manager->findUserByUsernameOrEmail('JaCk@EmAiL.oRg');
    }

    private function getMockCanonicalizer()
    {
        return $this->getMock('FOS\UserBundle\Util\CanonicalizerInterface');
    }

    private function getMockEncoderFactory()
    {
        return $this->getMock('Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface');
    }

    private function getMockPasswordEncoder()
    {
        return $this->getMock('Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface');
    }

    private function getUser()
    {
        return $this->getMockBuilder('FOS\UserBundle\Model\User')
            ->getMockForAbstractClass();
    }

    private function getUserManager(array $args)
    {
        return $this->getMockBuilder('FOS\UserBundle\Model\UserManager')
            ->setConstructorArgs($args)
            ->getMockForAbstractClass();
    }
}
