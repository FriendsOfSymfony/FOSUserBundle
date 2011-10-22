<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Propel;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManager as BaseUserManager;
use FOS\UserBundle\Propel\User;
use FOS\UserBundle\Util\CanonicalizerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\UserInterface as SecurityUserInterface;
use Symfony\Component\Validator\Constraint;

class UserManager extends BaseUserManager
{
    protected $class;

    protected $proxyClass;

    /**
     * Constructor.
     *
     * @param EncoderFactoryInterface $encoderFactory
     * @param string                  $algorithm
     * @param CanonicalizerInterface  $usernameCanonicalizer
     * @param CanonicalizerInterface  $emailCanonicalizer
     * @param string                  $class
     */
    public function __construct(EncoderFactoryInterface $encoderFactory, $algorithm, CanonicalizerInterface $usernameCanonicalizer, CanonicalizerInterface $emailCanonicalizer, $proxyClass, $class)
    {
        parent::__construct($encoderFactory, $algorithm, $usernameCanonicalizer, $emailCanonicalizer);

        $this->class = $class;
        $this->proxyClass = $proxyClass;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteUser(UserInterface $user)
    {
        $user->delete();
    }

    public function refreshUser(SecurityUserInterface $user)
    {
        if (!$user instanceof $this->proxyClass) {
            throw new UnsupportedUserException('Account is not supported.');
        }

        return $this->loadUserByUsername($user->getUsername());
    }


    /**
    * Returns an empty user instance
    *
    * @return UserInterface
    */
    public function createUser()
    {
        $class = $this->getClass();
        $user = new $class();
        $user->setAlgorithm($this->algorithm);

        return $this->proxyfy($user);
    }

    public function getProxyClass()
    {
        return $this->proxyClass;
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
        $query = $this->createQuery();

        foreach ($criteria as $field => $value) {
            $method = 'filterBy'.ucfirst($field);
            $query->$method($value);
        }

        $user = $query->findOne();

        if ($user) {
            $user = $this->proxyfy($user);
        }

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function findUsers()
    {
        return $this->createQuery()->find();
    }

    /**
     * {@inheritDoc}
     */
    public function reloadUser(UserInterface $user)
    {
        $user->reload();
    }

    /**
     * Updates a user.
     *
     * @param UserInterface $user
     */
    public function updateUser(UserInterface $user)
    {
        $this->updateCanonicalFields($user);
        $this->updatePassword($user);
        $user->save();
    }

    /**
     * {@inheritDoc}
     */
    public function validateUnique(UserInterface $value, Constraint $constraint)
    {
        // Since we probably want to validate the canonical fields,
        // we'd better make sure we have them.
        $this->updateCanonicalFields($value);

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
     * Indicates whether the given user and all compared objects correspond to the same record.
     *
     * @param UserInterface $user
     * @param array $comparisons
     * @return Boolean
     */
    protected function anyIsUser($user, array $comparisons)
    {
        foreach ($comparisons as $comparison) {
            foreach ($comparison as $field => $value) {
                if ($user->{'get'.$field}() !== $value) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Gets conflictual users for the given user and constraint.
     *
     * @param UserInterface $value
     * @param array $fields
     * @return array
     */
    protected function findConflictualUsers($value, array $fields)
    {
        $query = $this->createQuery();

        foreach ($fields as $field) {
            $method = 'get'.ucfirst($field);
            $query->filterBy(ucfirst($field), $value->$method());
        }

        return $query->find()->toArray();
    }

    /**
     * Create the propel query class corresponding to your queryclass
     *
     * @return \ModelCriteria the queryClass
     */
    protected function createQuery()
    {
        return \PropelQuery::from($this->class);
    }

    /**
     * Gets the criteria used to find conflictual entities.
     *
     * @param UserInterface $value
     * @param array $constraint
     * @return array
     */
    protected function getCriteria($value, array $fields)
    {
        $criteria = array();
        foreach ($fields as $field) {
            $criteria[$field] = $value;
        }

        return $criteria;
    }

    protected function proxyfy(User $user)
    {
        $proxyClass = $this->getProxyClass();
        $proxy = new $proxyClass($user);

        return $proxy;
    }
}
