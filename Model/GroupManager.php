<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Model;

@trigger_error('Using Groups is deprecated since version 2.2 and will be removed in 3.0.', E_USER_DEPRECATED);

/**
 * Abstract Group Manager implementation which can be used as base class for your
 * concrete manager.
 *
 * @author Christophe Coevoet <stof@notk.org>
 *
 * @deprecated
 */
abstract class GroupManager implements GroupManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function createGroup($name)
    {
        $class = $this->getClass();

        return new $class($name);
    }

    /**
     * {@inheritdoc}
     */
    public function findGroupByName($name)
    {
        return $this->findGroupBy(['name' => $name]);
    }
}
