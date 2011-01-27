<?php

namespace FOS\UserBundle\Entity;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManager as BaseUserManager;
use FOS\UserBundle\Util\CanonicalizerInterface;
use Symfony\Component\Security\Encoder\EncoderFactoryInterface;
use Symfony\Component\Validator\Constraint;

class UserManager extends BaseUserManager
{
    protected $em;
    protected $class;
    protected $repository;

    /**
     * Constructor.
     *
     * @param EncoderFactoryInterface $encoderFactory
     * @param string                  $algorithm
     * @param CanonicalizerInterface  $usernameCanonicalizer
     * @param CanonicalizerInterface  $emailCanonicalizer
     * @param EntityManager           $em
     * @param string                  $class
     */
    public function __construct(EncoderFactoryInterface $encoderFactory, $algorithm, CanonicalizerInterface $usernameCanonicalizer, CanonicalizerInterface $emailCanonicalizer, EntityManager $em, $class)
    {
        parent::__construct($encoderFactory, $algorithm, $usernameCanonicalizer, $emailCanonicalizer);

        $this->em = $em;
        $this->repository = $em->getRepository($class);

        $metadata = $em->getClassMetadata($class);
        $this->class = $metadata->name;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteUser(UserInterface $user)
    {
        $this->em->remove($user);
        $this->em->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritDoc}
     */
    public function findUserBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function findUsers()
    {
        return $this->repository->findAll();
    }

    /**
     * {@inheritDoc}
     */
    public function updateUser(UserInterface $user)
    {
        $this->updateCanonicalFields($user);
        $this->updatePassword($user);

        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function validateUnique($value, Constraint $constraint)
    {
        $fields = array_map('trim', explode(',', $constraint->property));
        $users = $this->findConflictualUsers($value, $fields);

        // there is no conflictual user
        if (empty($users)) {
            return true;
        }

        // there is no conflictual user which is not the same as the value
        if ($this->anyIsUser($value, $users)) {
            return true;
        }

        return false;
    }

    /**
     * Indicates whether the given user and all compared objects correspond to the same record
     *
     * @param UserInterface $user
     * @param array $comparisons
     * @return boolean
     */
    protected function anyIsUser($user, array $comparisons)
    {
        foreach ($comparisons as $comparison) {
            if (!$user->isUser($comparison)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Gets conflictual users for the given user and constraint
     *
     * @param UserInterface|string $value
     * @param array $fields
     * @return array
     */
    protected function findConflictualUsers($value, array $fields)
    {
        return $this->repository->findBy($this->getCriteria($value, $fields));
    }

    /**
     * Gets the criteria used to find conflictual entities
     *
     * @param UserInterface|string $value
     * @param array $constraint
     * @return array
     */
    protected function getCriteria($value, array $fields)
    {
        $classMetadata = $this->em->getClassMetadata($this->class);

        $criteria = array();
        foreach ($fields as $field) {
            if (!$classMetadata->hasField($field)) {
                throw new \InvalidArgumentException(sprintf('The "%s" class metadata does not have any "%s" field or association mapping.', $this->class, $field));
            }

            $criteria[$field] = $classMetadata->getFieldValue($value, $field);
        }

        return $criteria;
    }
}
