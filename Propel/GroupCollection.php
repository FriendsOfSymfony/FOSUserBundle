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

use \Doctrine\Common\Collections\Collection;

class GroupCollection implements Collection
{
    private $collection;

    public function __construct(\PropelCollection $collection)
    {
        $this->collection = $collection;
    }

    public function add($element)
    {
        $this->collection->append($element);

        return true;
    }

    public function clear()
    {
        $this->collection->clear();
    }

    public function contains($element)
    {
        return $this->collection->contains($element);
    }

    public function isEmpty()
    {
        return $this->collection->isEmpty();
    }

    public function remove($key)
    {
        $this->collection->remove($key);
    }

    public function removeElement($element)
    {
        $key = $this->collection->search($element);

        if (false !== $key) {
            $this->collection->remove($key);

            return true;
        }

        return false;
    }

    public function containsKey($key)
    {
        $this->collection->offsetExists($key);
    }

    public function get($key)
    {
        return $this->collection->get($key);
    }

    public function getKeys()
    {
        return array_keys($this->collection->getArrayCopy());
    }

    public function getValues()
    {
        return array_values($this->toArray());
    }

    public function set($key, $value)
    {
        $this->collection->set($key, $value);
    }

    public function toArray()
    {
        return $this->collection->getArrayCopy();
    }

    public function first()
    {
        return $this->collection->getFirst();
    }

    public function last()
    {
        return $this->collection->getLast();
    }

    public function key()
    {
        return $this->collection->getPosition();
    }

    public function current()
    {
        return $this->collection->getCurrent();
    }

    public function next()
    {
        return $this->collection->getNext();
    }

    public function exists(\Closure $p)
    {
        foreach ($this->collection as $key => $element) {
            if ($p($key, $element)) {
                return true;
            }
        }

        return false;
    }

    public function filter(\Closure $p)
    {
        // TODO: Implement filter() method.
        throw new \BadMethodCallException('This method is not implemented by the GroupCollection.');
    }

    public function forAll(\Closure $p)
    {
        foreach ($this->collection as $key => $element) {
            if (!$p($key, $element)) {
                return false;
            }
        }

        return true;
    }

    public function map(\Closure $func)
    {
        // TODO: Implement map() method.
        throw new \BadMethodCallException('This method is not implemented by the GroupCollection.');
    }

    public function partition(\Closure $p)
    {
        // TODO: Implement partition() method.
        throw new \BadMethodCallException('This method is not implemented by the GroupCollection.');
    }

    public function indexOf($element)
    {
        return $this->collection->search($element);
    }

    public function slice($offset, $length = null)
    {
        return array_slice($this->toArray(), $offset, $length, true);
    }

    public function offsetExists($offset)
    {
        return $this->collection->offsetExists($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->collection->offsetUnset($offset);
    }

    public function getIterator()
    {
        return $this->collection->getIterator();
    }

    public function count()
    {
        return $this->collection->count();
    }
}
