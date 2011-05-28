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
    private $algorithm;
    private $usernameCanonicalizer;
    private $emailCanonicalizer;

    protected function setUp()
    {
        $this->encoderFactory        = $this->getMockEncoderFactory();
        $this->algorithm             = 'sha512';
        $this->usernameCanonicalizer = $this->getMockCanonicalizer();
        $this->emailCanonicalizer    = $this->getMockCanonicalizer();

        $this->manager = $this->getUserManager(array(
            $this->encoderFactory,
            $this->algorithm,
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
        $this->assertEquals($this->algorithm, $user->getAlgorithm(), '->updatePassword() sets algorithm');
        $this->assertEquals('encodedPassword', $user->getPassword(), '->updatePassword() sets encoded password');
        $this->assertNull($user->getPlainPassword(), '->updatePassword() erases credentials');
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
