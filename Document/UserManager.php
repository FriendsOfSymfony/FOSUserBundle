<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Document;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Proxy\Proxy;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManager as BaseUserManager;
use FOS\UserBundle\Util\CanonicalizerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Validator\Constraint;

class UserManager extends BaseUserManager
{
    protected $dm;
    protected $repository;
    protected $class;

    /**
     * Constructor.
     *
     * @param EncoderFactoryInterface $encoderFactory
     * @param CanonicalizerInterface  $usernameCanonicalizer
     * @param CanonicalizerInterface  $emailCanonicalizer
     * @param DocumentManager         $dm
     * @param string                  $class
     */
    public function __construct(EncoderFactoryInterface $encoderFactory, CanonicalizerInterface $usernameCanonicalizer, CanonicalizerInterface $emailCanonicalizer, DocumentManager $dm, $class)
    {
        parent::__construct($encoderFactory, $usernameCanonicalizer, $emailCanonicalizer);

        $this->dm = $dm;
        $this->repository = $dm->getRepository($class);

        $metadata = $dm->getClassMetadata($class);
        $this->class = $metadata->name;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteUser(UserInterface $user)
    {
        $this->dm->remove($user);
        $this->dm->flush();
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
    public function reloadUser(UserInterface $user)
    {
        $this->dm->refresh($user);
    }

    /**
     * Updates a user.
     *
     * @param UserInterface $user
     * @param Boolean       $andFlush Whether to flush the changes (default true)
     */
    public function updateUser(UserInterface $user, $andFlush = true)
    {
        $this->updateCanonicalFields($user);
        $this->updatePassword($user);

        $this->dm->persist($user);
        if ($andFlush) {
            $this->dm->flush();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function validateUnique(UserInterface $value, Constraint $constraint)
    {
        // Since we probably want to validate the canonical fields,
        // we'd better make sure we have them.
        $this->updateCanonicalFields($value);

        $classMetadata = $this->dm->getClassMetadata($this->class);
        // TODO: ODM seems to be missing handling for multiple properties
        // $fields = array_map('trim', explode(',', $constraint->property));
        $query = $this->getQueryArray($classMetadata, $value, $constraint->property);

        $document = $this->findUserBy($query);
        if (null === $document) {
            return true;
        }

        // check if document in mongodb is the same document as the checked one
        if ($document->isUser($value)) {
            return true;
        }
        // check if returned document is proxy and initialize the minimum identifier if needed
        if ($document instanceof Proxy) {
            $classMetadata->setIdentifierValue($document, $document->__identifier);
        }
        // check if document has the same identifier as the current one
        if ($classMetadata->getIdentifierValue($document) === $classMetadata->getIdentifierValue($value)) {
            return true;
        }

        return false;
    }

    protected function getQueryArray($classMetadata, $value, $fieldName)
    {
        $field = $this->getFieldNameFromPropertyPath($fieldName);
        if (!isset($classMetadata->fieldMappings[$field])) {
            throw new \LogicException("Mapping for '$fieldName' doesn't exist for " . $this->class);
        }

        $mapping = $classMetadata->fieldMappings[$field];
        if (isset($mapping['reference']) && $mapping['reference']) {
            throw new \LogicException('Cannot determine uniqueness of referenced document values');
        }

        $criteria[$field] = $value instanceOf UserInterface ? $classMetadata->getFieldValue($value, $field) : $value;

        return $criteria;
    }

    /**
     * Returns the actual document field value.
     *
     * E.g. document.someVal -> document
     *      user.emails      -> user
     *      username         -> username
     *
     * @param string $field
     * @return string
     */
    protected function getFieldNameFromPropertyPath($field)
    {
        $pieces = explode('.', $field);

        return $pieces[0];
    }
}
