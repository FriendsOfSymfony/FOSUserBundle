<?php

namespace FOS\UserBundle\Util;

/**
 * @internal
 *
 * @author Jonny Schmid <jonny@fourlabs.co.uk>
 */
final class RolesHelper
{
    /**
     * @var array
     */
    private $roles;

    /**
     * Constructor.
     *
     * @param array $rolesHierarchy
     */
    public function __construct($rolesHierarchy)
    {
        $roles = array();

        array_walk_recursive($rolesHierarchy, function($val) use (&$roles) {
            $roles[] = $val;
        });

        $this->roles = array_unique($roles);
    }

    /**
     * @return array Array of unique role names
     */
    public function getRoles()
    {
        return $this->roles;
    }
}
