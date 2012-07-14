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

class UserManager extends BaseUserManager
{
    protected $class;

    protected $modelClass;

    /**
     * Constructor.
     *
     * @param EncoderFactoryInterface $encoderFactory
     * @param CanonicalizerInterface  $usernameCanonicalizer
     * @param CanonicalizerInterface  $emailCanonicalizer
     * @param string                  $proxyClass
     * @param string                  $modelClass
     */
    public function __construct(EncoderFactoryInterface $encoderFactory, CanonicalizerInterface $usernameCanonicalizer, CanonicalizerInterface $emailCanonicalizer, $proxyClass, $modelClass)
    {
        parent::__construct($encoderFactory, $usernameCanonicalizer, $emailCanonicalizer);

        $this->class = $proxyClass;
        $this->modelClass = $modelClass;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteUser(UserInterface $user)
    {
        if (!$user instanceof UserProxy) {
            throw new \InvalidArgumentException('This user instance is not supported by the Propel UserManager implementation');
        }

        $user->delete();
    }

    /**
    * Returns an empty user instance
    *
    * @return UserInterface
    */
    public function createUser()
    {
        $class = $this->modelClass;
        $user = new $class();

        return $this->proxyfy($user);
    }

    public function getModelClass()
    {
        return $this->modelClass;
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
        if (!$user instanceof UserProxy) {
            throw new \InvalidArgumentException('This user instance is not supported by the Propel UserManager implementation');
        }

        $user->reload();
    }

    /**
     * Updates a user.
     *
     * @param UserInterface $user
     */
    public function updateUser(UserInterface $user)
    {
        if (!$user instanceof UserProxy) {
            throw new \InvalidArgumentException('This user instance is not supported by the Propel UserManager implementation');
        }

        $this->updateCanonicalFields($user);
        $this->updatePassword($user);
        $user->save();
    }

    /**
     * Create the propel query class corresponding to your queryclass
     *
     * @return \ModelCriteria the queryClass
     */
    protected function createQuery()
    {
        return \PropelQuery::from($this->modelClass);
    }

    protected function proxyfy($user)
    {
        $proxyClass = $this->getClass();
        $proxy = new $proxyClass($user);

        return $proxy;
    }
}
