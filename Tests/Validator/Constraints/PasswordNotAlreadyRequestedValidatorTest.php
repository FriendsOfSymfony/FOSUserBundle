<?php

namespace FOS\UserBundle\Tests\Validator\Constraints;

use FOS\UserBundle\Form\Model\ResettingRequest;
use FOS\UserBundle\Validator\Constraints\PasswordNotAlreadyRequested;
use FOS\UserBundle\Validator\Constraints\PasswordNotAlreadyRequestedValidator;

class PasswordNotAlreadyRequestedValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testValidationWithPasswordAlreadyRequested()
    {
        $identifier = 'valid_username';

        $ttl = 1;

        $userMock = $this->getMock('\FOS\UserBundle\Model\UserInterface');
        $userMock->expects($this->once())
            ->method('isPasswordRequestNonExpired')
            ->will($this->returnValue(true))
            ->with($ttl)
        ;

        $userProviderMock = $this->getMock('Symfony\Component\Security\Core\User\UserProviderInterface');
        $userProviderMock->expects($this->once())
            ->method('loadUserByUsername')
            ->will($this->returnValue($userMock))
            ->with($identifier)
        ;

        $contextMock = $this->getMock('Symfony\Component\Validator\ExecutionContextInterface');
        $contextMock->expects($this->once())
            ->method('addViolation')
        ;

        $resettingRequest = new ResettingRequest();
        $resettingRequest->identifier = $identifier;

        $userExistValidator = new PasswordNotAlreadyRequestedValidator($userProviderMock, $ttl);
        $userExistValidator->initialize($contextMock);
        $userExistValidator->validate($resettingRequest, new PasswordNotAlreadyRequested());
    }
}
