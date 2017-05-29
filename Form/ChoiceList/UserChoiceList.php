<?php

namespace FOS\UserBundle\Form\ChoiceList;

use Symfony\Component\Form\Exception\RuntimeException;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Model\GroupManagerInterface;

class UserChoiceList extends ObjectChoiceList
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var GroupManagerInterface
     */
    protected $groupManager;

    /**
     * @var \Closure
     */
    protected $loader;

    /**
     * @var Boolean
     */
    private $loaded = false;

    /**
     * @var array
     */
    private $preferredUsers;

    /**
     * Creates a new user choice list.
     *
     * @param UserManagerInterface     $userManager
     * @param GroupManagerInterface    $groupManager
     * @param string                    $labelPath
     * @param array                     $users
     * @param array                     $preferredUsers
     * @param string                    $groupPath
     * @param PropertyAccessorInterface $propertyAccessor
     */
    public function __construct(UserManagerInterface $userManager, GroupManagerInterface $groupManager, \Closure $loader = null, $labelPath = null, $users = null, array $preferredUsers = array(), $groupPath = null, PropertyAccessorInterface $propertyAccessor = null)
    {
        $this->userManager = $userManager;
        $this->groupManager = $groupManager;
        $this->loader = $loader;
        $this->loaded = is_array($users) || $users instanceof \Traversable;
        $this->preferredUsers = $preferredUsers;

        if (!$this->loaded) {
            // Make sure the constraints of the parent constructor are
            // fulfilled
            $users = array();
        }

        parent::__construct($users, $labelPath, $preferredUsers, $groupPath, null, $propertyAccessor);
    }

    public function getChoices()
    {
        if (!$this->loaded) {
            $this->load();
        }

        return parent::getChoices();
    }

    public function getValues()
    {
        if (!$this->loaded) {
            $this->load();
        }

        return parent::getValues();
    }

    public function getPreferredViews()
    {
        if (!$this->loaded) {
            $this->load();
        }

        return parent::getPreferredViews();
    }

    public function getRemainingViews()
    {
        if (!$this->loaded) {
            $this->load();
        }

        return parent::getRemainingViews();
    }

    public function getChoicesForValues(array $values)
    {
        // Performance optimization
        if (empty($values)) {
            return array();
        }

        if (!$this->loaded) {
            $users = array();

            foreach ($this->fixValues($values) as $i => $value) {
                $users[$i] = $this->userManager->loadUserByUsername($value);
            }

            return $users;
        }

        return parent::getChoicesForValues($values);
    }

    public function getValuesForChoices(array $choices)
    {
        // Performance optimization
        if (empty($choices)) {
            return array();
        }

        if (!$this->loaded) {
            $values = array();

            foreach ($this->fixChoices($choices) as $i => $choice) {
                $values[$i] = $this->createValue($choice);
            }

            return $values;
        }

        return parent::getValuesForChoices($choices);
    }

    public function getIndicesForChoices(array $users)
    {
        // Performance optimization
        if (empty($values)) {
            return array();
        }

        if (!$this->loaded) {
            $indices = array();

            foreach ($users as $i => $user) {
                $indices[$i] = $this->createIndex($user);
            }

            return $indices;
        }

        return parent::getIndicesForChoices($users);
    }

    public function getIndicesForValues(array $values)
    {
        // Performance optimization
        if (empty($values)) {
            return array();
        }

        if (!$this->loaded) {
            return $this->fixIndices($values);
        }

        return parent::getIndicesForValues($values);
    }

    protected function createIndex($user)
    {
        return $this->fixIndex($user ? $user->getUsername() : '');
    }

    protected function createValue($user)
    {
        return $this->fixValue($user ? $user->getUsername() : '');
    }

    private function load()
    {
        if ($this->loader) {
            $users = call_user_func($this->loader, $this->userManager, $this->groupManager); 
        } else {
            $users = $this->userManager->findUsers();
        }

        // The second parameter $labels is ignored by ObjectChoiceList
        parent::initialize($users, array(), $this->preferredUsers);

        $this->loaded = true;
    }

    protected function fixIndex($index)
    {
        $index = parent::fixIndex($index);

        // index. Replace any at-sign or period by underscore to make it a valid
        // form name.
        $index = strtr($index, '@.', '__');

        return $index;
    }
}