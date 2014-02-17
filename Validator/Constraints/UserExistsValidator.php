<?php

namespace FOS\UserBundle\Validator\Constraints;

use FOS\UserBundle\Form\Model\ResettingRequest;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UserExistsValidator extends ConstraintValidator
{
    /** @var \Symfony\Component\Security\Core\User\UserProviderInterface  */
    private $userProvider;

    public function __construct(UserProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param ResettingRequest $user The value that should be validated
     * @param UserExists $constraint The constraint for the validation
     *
     * @api
     */
    public function validate($user, Constraint $constraint)
    {
        try {
            $this->userProvider->loadUserByUsername($user->identifier);
        } catch (UsernameNotFoundException $e) {
            $this->context->addViolation($constraint->message, array('%username%' => $user->identifier));
        }

    }
}
