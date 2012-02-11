<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Validator;

use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PasswordValidator extends ConstraintValidator
{
    protected $encoderFactory;

    public function setEncoderFactory(EncoderFactoryInterface $factory)
    {
        $this->encoderFactory = $factory;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed      $object     The object that should be validated
     * @param Constraint $constraint The constrain for the validation
     *
     * @return Boolean Whether or not the value is valid
     *
     * @throws UnexpectedTypeException if $object is not an object
     */
    public function isValid($object, Constraint $constraint)
    {
        if (!is_object($object)) {
            throw new UnexpectedTypeException($object, 'object');
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
