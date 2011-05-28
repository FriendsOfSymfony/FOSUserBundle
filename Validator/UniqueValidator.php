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
use Symfony\Component\Validator\Exception\ValidatorException;

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
     * Sets the user manager
     *
     * @param UserManagerInterface $userManager
     */
    public function setUserManager(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * Gets the user manager
     *
     * @return UserManagerInterface
     */
    public function getUserManager()
    {
        return $this->userManager;
    }

    /**
     * Indicates whether the constraint is valid
     *
     * @param Entity     $value
     * @param Constraint $constraint
     */
    public function isValid($value, Constraint $constraint)
    {
        if (!$this->getUserManager()->validateUnique($value, $constraint)) {
            $this->setMessage($constraint->message, array(
                '%property%' => $constraint->property
            ));
            return false;
        }

        return true;
    }
}
