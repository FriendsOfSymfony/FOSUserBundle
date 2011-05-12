<?php

/*
 * This file is part of the Symfony FOSUB
 *
 * (c) Luis Cordova <cordoval@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace FOS\UserBundle\Tests\Validation;

use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Validator\PasswordValidator;
use FOS\UserBundle\Validator\Password;
use FOS\UserBundle\Tests\TestUser;
use FOS\UserBundle\Form\ChangePassword;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class PasswordValidatorTest extends \PHPUnit_Framework_TestCase
{
    protected $validator;
    protected $userManagerMock;
    protected $constraint;
    protected $encoderFactory;
    protected $userObject;
    protected $changePasswordObject;
    protected $nonObjectProperty;
    protected $encoder;

    public function setUp()
    {
        $this->userManagerMock = $this->createUserManagerMock(array('validateUnique'));
        $this->constraintMock = new Password();//$this->createPasswordConstraintMock(array());
        $this->constraintMock->passwordProperty = 'current';
        $this->validator = new PasswordValidator();
        $this->encoderFactory = $this->createEncoderFactoryMock(array('getEncoder'));
        $this->encoder = $this->createEncoderMock(array());
        $this->validator->setEncoderFactory($this->encoderFactory);
        $this->userObject = $this->createUserMock(array('getPassword', 'getSalt'));
        $this->constraintMock->userProperty = 'user'; //$this->userObject;
        $this->nonObjectProperty = 'propertyValue';
        $this->changePasswordObject = $this->createchangePasswordObjectMock(array());
        $this->changePasswordObject->user = $this->userObject;
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testExceptionForNonObject()
    {
        $this->validator->isValid($this->nonObjectProperty, $this->constraintMock);
    }

    public function testFalseOnInvalidPassword()
    {
        $this->encoderFactory->expects($this->once())
                ->method('getEncoder')
                ->will($this->returnValue($this->encoder));

        $this->encoder->expects($this->once())
                ->method('isPasswordValid')
                ->will($this->returnValue(false));

        $this->validator->setEncoderFactory($this->encoderFactory);
        $this->assertFalse($this->validator->isValid($this->changePasswordObject, $this->constraintMock));
    }

    public function testTrueOnValidPassword()
    {
        $this->encoderFactory->expects($this->once())
                ->method('getEncoder')
                ->will($this->returnValue($this->encoder));

        $this->encoder->expects($this->once())
                ->method('isPasswordValid')
                ->will($this->returnValue(true));

        $this->validator->setEncoderFactory($this->encoderFactory);
        $this->assertTrue($this->validator->isValid($this->changePasswordObject, $this->constraintMock));
    }

    protected function createUserManagerMock(array $methods)
    {
        $userManager = $this->getMockBuilder('FOS\UserBundle\Model\UserManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        return $userManager;
    }

    protected function createPasswordConstraintMock(array $methods)
    {
        $constraint = $this->getMockBuilder('FOS\UserBundle\Validator\Password')
            //->disableOriginalConstructor()
            ->getMock();
        
        return $constraint;
    }

    protected function createUserMock(array $methods)
    {
        $constraint = $this->getMockBuilder('FOS\UserBundle\Model\User')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        return $constraint;
    }

    protected function createEncoderFactoryMock(array $methods)
    {
        $encoderFactory = $this->getMockBuilder('Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface')
           ->disableOriginalConstructor()
           ->getMock();
        
        return $encoderFactory;
    }

    protected function createchangePasswordObjectMock(array $methods)
    {
        $changePasswordObject = $this->getMockBuilder('FOS\UserBundle\Form\ChangePassword')
           ->disableOriginalConstructor()
           ->getMock();

        return $changePasswordObject;
    }

    protected function createEncoderMock(array $methods)
    {
        $encoder = $this->getMockBuilder('Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface')
           ->disableOriginalConstructor()
           ->getMock();

        return $encoder;
    }
}
