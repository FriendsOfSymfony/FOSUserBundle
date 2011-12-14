<?php

namespace FOS\UserBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

/**
 * Transforms between a UserInterface and a username
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class UsernameToUserTransformer implements DataTransformerInterface
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
     * Transforms a UserInterface instance to a username string
     *
     * @param mixed $value a UserInterface instance
     *
     * @return string the username
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
     * Transforms a username to a UserInterface instance
     *
     * @param string $value username
     *
     * @return UserInterface the corresponding user instance
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return null;
        }
        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        return $this->userManager->findUserByUsername($value);
    }
}
