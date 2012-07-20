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
use FOS\UserBundle\Util\CanonicalizerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class UserManager extends BaseUserManager
{
    protected $class;

    /**
     * Constructor.
     *
     * @param EncoderFactoryInterface $encoderFactory
     * @param CanonicalizerInterface  $usernameCanonicalizer
     * @param CanonicalizerInterface  $emailCanonicalizer
     * @param string                  $class
     */
    public function __construct(EncoderFactoryInterface $encoderFactory, CanonicalizerInterface $usernameCanonicalizer, CanonicalizerInterface $emailCanonicalizer, $class)
    {
        parent::__construct($encoderFactory, $usernameCanonicalizer, $emailCanonicalizer);

        $this->class = $class;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteUser(UserInterface $user)
    {
        if (!$user instanceof \Persistent) {
            throw new \InvalidArgumentException('This user instance is not supported by the Propel UserManager implementation');
        }

        $user->delete();
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

        return $query->findOne();
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
        if (!$user instanceof \Persistent) {
            throw new \InvalidArgumentException('This user instance is not supported by the Propel UserManager implementation');
        }

        $user->reload();
    }

    /**
     * {@inheritDoc}
     */
    public function updateUser(UserInterface $user)
    {
        if (!$user instanceof \Persistent) {
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
        return \PropelQuery::from($this->class);
    }
}
