<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Tests\Security\Encoder;

use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use FOS\UserBundle\Security\Encoder\EncoderFactory;

class EncoderFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers FOS\UserBundle\Security\Encoder\EncoderFactory::getEncoder
     * @covers FOS\UserBundle\Security\Encoder\EncoderFactory::createEncoder
     */
    public function testGetEncoderWithUserAccount()
    {
        $factory = new EncoderFactory(
            'Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder',
            false,
            1,
            $this->getMock('Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface')
        );

        $userAccount = $this->getMock('FOS\UserBundle\Model\UserInterface');

        $userAccount->expects($this->once())
            ->method('getAlgorithm')
            ->will($this->returnValue('sha512'));

        $encoder = $factory->getEncoder($userAccount);

        $expectedEncoder = new MessageDigestPasswordEncoder('sha512', false, 1);

        $this->assertEquals($expectedEncoder->encodePassword('foo', 'bar'), $encoder->encodePassword('foo', 'bar'));
    }

    public function testGetEncoderWithGenericAccount()
    {
        $genericFactory = $this->getMock('Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface');
        $encoder = $this->getMock('Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface');

        $genericFactory
            ->expects($this->once())
            ->method('getEncoder')
            ->will($this->returnValue($encoder))
        ;

        $factory = new EncoderFactory(null , false, 1, $genericFactory);

        $this->assertSame($encoder, $factory->getEncoder($this->getMock('Symfony\Component\Security\Core\User\UserInterface')));
    }
}
