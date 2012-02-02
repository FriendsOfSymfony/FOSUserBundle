<?php

namespace FOS\UserBundle\Form\DataTransformer;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

/**
 * Transforms between a UserInterface instance and a username string.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class UserToUsernameTransformer implements DataTransformerInterface
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * Transforms a UserInterface instance into a username string.
     *
     * @param mixed $value UserInterface instance
     *
     * @return string Username
     *
     * @throws UnexpectedTypeException if the given value is not a UserInterface instance
     */
    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof UserInterface) {
            throw new UnexpectedTypeException($value, 'FOS\UserBundle\Model\UserInterface');
        }

        return $value->getUsername();
    }

    /**
     * Transforms a username string into a UserInterface instance.
     *
     * @param string $value Username
     *
     * @return UserInterface the corresponding UserInterface instance
     *
     * @throws UnexpectedTypeException if the given value is not a string
     */
    public function reverseTransform($value)
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        return $this->userManager->findUserByUsername($value);
    }
}
