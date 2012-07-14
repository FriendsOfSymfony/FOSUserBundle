<?php

namespace FOS\UserBundle\CouchDocument;

use FOS\UserBundle\Model\GroupInterface;
use Doctrine\ODM\CouchDB\DocumentManager;
use FOS\UserBundle\Doctrine\GroupManager as BaseGroupManager;

/**
 * BC class for people extending it in their bundle.
 * TODO Remove this class on July 31st
 */
class GroupManager extends BaseGroupManager
{
    /**
     * @var DocumentManager
     */
    protected $dm;

    public function __construct(DocumentManager $dm, $class)
    {
        parent::__construct($dm, $class);
        $this->dm = $dm;
    }
}
