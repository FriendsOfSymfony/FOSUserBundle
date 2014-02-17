<?php

namespace FOS\UserBundle\Validator\Constraints;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PasswordNotAlreadyRequestedValidator extends ConstraintValidator
{
    /** @var \Symfony\Component\Security\Core\User\UserProviderInterface  */
    private $userProvider;

    private $ttl;

    public function __construct(UserProviderInterface $userProvider, $ttl)
    {
        $this->userProvider = $userProvider;
        $this->ttl = $ttl;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param ResettingRequest $value The value that should be validated
     * @param UserExists $constraint The constraint for the validation
     *
     * @api
     */
    public function validate($identifier, Constraint $constraint)
    {
        /** @var UserInterface $userEntity */
        $userEntity = $this->userProvider->loadUserByUsername($identifier);

        if ($userEntity->isPasswordRequestNonExpired($this->ttl)) {
            $this->context->addViolation($constraint->message);
        }
    }
}
