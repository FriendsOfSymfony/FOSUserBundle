<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Tests\Validation;

use FOS\UserBundle\Validator\PasswordValidator;
use FOS\UserBundle\Validator\Password;
use FOS\UserBundle\Form\ChangePassword;

class PasswordValidatorTest extends \PHPUnit_Framework_TestCase
{
    private $validator;
    private $constraint;
    private $encoderFactory;
    private $changePasswordObject;
    private $encoder;

    public function setUp()
    {
        $this->constraint = new Password();
        $this->constraint->passwordProperty = 'current';
        $this->constraint->userProperty = 'user';

        $this->changePasswordObject = new ChangePassword($this->getMock('FOS\UserBundle\Model\UserInterface'));
        $this->encoderFactory = $this->getMock('Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface');
        $this->encoder = $this->getMock('Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface');

        $this->validator = new PasswordValidator();
        $this->validator->setEncoderFactory($this->encoderFactory);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testExceptionForNonObject()
    {
        $this->validator->isValid('propertyValue', $this->constraint);
    }

    public function testFalseOnInvalidPassword()
    {
        $this->encoderFactory->expects($this->once())
                ->method('getEncoder')
                ->will($this->returnValue($this->encoder));

        $this->encoder->expects($this->once())
                ->method('isPasswordValid')
                ->will($this->returnValue(false));

        $this->assertFalse($this->validator->isValid($this->changePasswordObject, $this->constraint));
    }

    public function testTrueOnValidPassword()
    {
        $this->encoderFactory->expects($this->once())
                ->method('getEncoder')
                ->will($this->returnValue($this->encoder));

        $this->encoder->expects($this->once())
                ->method('isPasswordValid')
                ->will($this->returnValue(true));

        $this->assertTrue($this->validator->isValid($this->changePasswordObject, $this->constraint));
    }
}
