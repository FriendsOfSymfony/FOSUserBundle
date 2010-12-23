<?php

namespace Bundle\FOS\UserBundle\Model;

interface RepositoryInterface
{
    /**
     * Get the Entity manager or the Document manager, depending on the db driver
     *
     * @return mixed
     **/
    function getObjectManager();

    /**
     * Get the class of the User Entity or Document, depending on the db driver
     *
     * @return string a model fully qualified class name
     **/
    function getObjectClass();

    /**
     * Get the identifier property of the Group
     *
     * @return string
     */
    function getObjectIdentifier();

    /**
     * Returns a fresh object instance
     *
     * @return object
     */
    function createObjectInstance();
}
