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

class ProxyfyingIterator implements \Iterator
{
    private $iterator;

    public function __construct(\Iterator $iterator)
    {
        $this->iterator = $iterator;
    }

    public function current()
    {
        $current = $this->iterator->current();
        if (!$current instanceof GroupProxy) {
            throw new \InvalidArgumentException(sprintf('A FOS\UserBundle\Propel\GroupCollection can only hold FOS\UserBundle\Propel\GroupProxy instances but got "%s"', get_class($current)));
        }

        return new GroupProxy($current);
    }

    public function next()
    {
        $this->iterator->next();
    }

    public function key()
    {
        return $this->iterator->key();
    }

    public function valid()
    {
        return $this->iterator->valid();
    }

    public function rewind()
    {
        $this->iterator->rewind();
    }
}
