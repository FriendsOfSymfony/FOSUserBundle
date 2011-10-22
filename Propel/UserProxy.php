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

use FOS\UserBundle\Model\User as ModelUser;

class UserProxy extends ModelUser
{
    protected $user;
    protected $roles;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->updateFormPropel();
    }

    public function getUser()
    {
        return $this->user;
    }

    public function __call($method, $arguments)
    {
        if (is_callable(array($this->getUser(), $method))) {
            return call_user_func_array(array($this->getUser(), $method), $arguments);
        }

        throw new \BadMethodCallException('Can\'t call method '.$method);
    }


    public function save()
    {
        $this->updatePropelUser();
        $this->getUser()->save();

        return $this;
    }

    protected function updateFormPropel()
    {
        $user = $this->getUser();

        $this->id = $user->getId();
        $this->username = $user->getUsername();
        $this->usernameCanonical = $user->getUsernameCanonical();
        $this->email = $user->getEmail();
        $this->emailCanonical = $user->getEmailCanonical();
        $this->enabled = $user->getEnabled();
        $this->algorithm = $user->getAlgorithm();
        $this->salt = $user->getSalt();
        $this->password = $user->getPassword();
        $this->lastLogin = $user->getLastLogin();
        $this->confirmationToken = $user->getConfirmationToken();
        $this->passwordRequestedAt = $user->getPasswordRequestedAt();
        $this->groups = $user->getGroups();
        $this->locked = $user->getLocked();
        $this->expiresAt = $user->getExpiresAt();

        $this->roles = array();
        foreach ($user->getRoles() as $role) {
            $this->roles[] = $role->getName();
        }

        $this->credentialsExpireAt = $user->getCredentialsExpireAt();

    }

    protected function updatePropelUser()
    {
        $user = $this->getUser();

        $user->setUsername($this->username);
        $user->setUsernameCanonical($this->usernameCanonical);
        $user->setEmail($this->email);
        $user->setEmailCanonical($this->emailCanonical);
        $user->setEnabled($this->enabled);
        $user->setAlgorithm($this->algorithm);
        $user->setSalt($this->salt);
        $user->setPassword($this->password);
        $user->setLastLogin($this->lastLogin);
        $user->setConfirmationToken($this->confirmationToken);
        $user->setPasswordRequestedAt($this->passwordRequestedAt);
        $user->setGroups($this->groups);
        $user->setLocked($this->locked);
        $user->setExpiresAt($this->expiresAt);

        $collection = new \PropelObjectCollection();
        $collection->setModel(RolePeer::OM_CLASS);
        foreach ($this->roles as $role) {
            $roleObject = RoleQuery::create()->findOneByName($role);
            if (!$roleObject) {
                $roleObject = new Role();
                $roleObject->setName($role);
                $roleObject->save();
            }

            $collection[] = $roleObject;
        }
        $user->setRoles($collection);

        $user->setCredentialsExpireAt($this->credentialsExpireAt);
    }
}
