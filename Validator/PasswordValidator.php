<?php

namespace FOS\UserBundle\Validator;

use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PasswordValidator extends ConstraintValidator
{
    protected $encoderFactory;

    public function setEncoderFactory(EncoderFactoryInterface $factory)
    {
        $this->encoderFactory = $factory;
    }

    public function isValid($object, Constraint $constraint)
    {
        if (!is_object($object)) {
            throw new \RuntimeException('This is a class constraint.');
        }
        $raw = $object->{$constraint->passwordProperty};
        $user = null === $constraint->userProperty ? $object : $object->{$constraint->userProperty};
        $encoder = $this->encoderFactory->getEncoder($user);
        if (!$encoder->isPasswordValid($user->getPassword(), $raw, $user->getSalt())) {
            $this->setMessage($constraint->message);
            return false;
        }

        return true;
    }
}
