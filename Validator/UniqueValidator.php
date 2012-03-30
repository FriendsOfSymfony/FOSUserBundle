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

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use FOS\UserBundle\Model\UserInterface;

/**
 * UniqueValidator
 */
class UniqueValidator extends ConstraintValidator
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * Constructor
     *
     * @param UserManagerInterface $userManager
     */
    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed      $value      The value that should be validated
     * @param Constraint $constraint The constrain for the validation
     *
     * @return Boolean Whether or not the value is valid
     *
     * @throws UnexpectedTypeException if $value is not instance of \FOS\UserBundle\Model\UserInterface
     */
    public function isValid($value, Constraint $constraint)
    {
        if (!$value instanceof UserInterface) {
            throw new UnexpectedTypeException($value, 'FOS\UserBundle\Model\UserInterface');
        }

        if (!$this->userManager->validateUnique($value, $constraint)) {
            $this->setMessage($constraint->message, array(
                '%property%' => $constraint->property
            ));

            return false;
        }

        return true;
    }
}
