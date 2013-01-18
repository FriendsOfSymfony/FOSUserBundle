<?php

namespace FOS\UserBundle\Form\DataTransformer;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

/**
 * Transforms between a UserInterface instance and a email string.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class UserToEmailTransformer implements DataTransformerInterface
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
     * Transforms a UserInterface instance into a email string.
     *
     * @param UserInterface|null $value UserInterface instance
     *
     * @return string|null Email
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

        return $value->getEmail();
    }

    /**
     * Transforms a email string into a UserInterface instance.
     *
     * @param string $value Email
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

        return $this->userManager->findUserByEmail($value);
    }
}
