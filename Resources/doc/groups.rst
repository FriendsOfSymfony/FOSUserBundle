Using Groups With FOSUserBundle
===============================

FOSUserBundle allows you to associate groups to your users. Groups are a
way to group a collection of roles. The roles of a group will be granted
to all users belonging to it.

.. note::

    Symfony supports role inheritance so inheriting roles from groups is
    not always needed. If the role inheritance is enough for your use case,
    it is better to use it instead of groups as it is more efficient (loading
    the groups triggers the database).

To use the groups, you need to explicitly enable this functionality in your
configuration. The only mandatory configuration is the fully qualified class
name (FQCN) of your ``Group`` class which must implement ``FOS\UserBundle\Model\GroupInterface``.

Below is an example configuration for enabling groups support.

.. configuration-block::

    .. code-block:: yaml

        # app/config/config.yml
        fos_user:
            db_driver: orm
            firewall_name: main
            user_class: AppBundle\Entity\User
            group:
                group_class: AppBundle\Entity\Group


    .. code-block:: xml

        <!-- app/config/config.xml -->
        <fos_user:config
            db-driver="orm"
            firewall-name="main"
            user-class="AppBundle\Entity\User"
        >
            <fos_user:group group-class="AppBundle\Entity\Group" />
        </fos_user:config>

The Group class
---------------

The simplest way to create a Group class is to extend the mapped superclass
provided by the bundle.

a) ORM Group class implementation
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. configuration-block::

    .. code-block:: php-annotations

        // src/MyProject/MyBundle/Entity/Group.php

        namespace MyProject\MyBundle\Entity;

        use FOS\UserBundle\Model\Group as BaseGroup;
        use Doctrine\ORM\Mapping as ORM;

        /**
         * @ORM\Entity
         * @ORM\Table(name="fos_group")
         */
        class Group extends BaseGroup
        {
            /**
             * @ORM\Id
             * @ORM\Column(type="integer")
             * @ORM\GeneratedValue(strategy="AUTO")
             */
             protected $id;
        }

    .. code-block:: yaml

        # src/AppBundle/Resources/config/doctrine/Group.orm.yml
        AppBundle\Entity\Group:
            type:  entity
            table: fos_group
            id:
                id:
                    type: integer
                    generator:
                        strategy: AUTO

.. note::

    ``Group`` is a reserved keyword in SQL so it cannot be used as the table name.

b) MongoDB Group class implementation
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    // src/MyProject/MyBundle/Document/Group.php

    namespace MyProject\MyBundle\Document;

    use FOS\UserBundle\Model\Group as BaseGroup;
    use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

    /**
     * @MongoDB\Document
     */
    class Group extends BaseGroup
    {
        /**
         * @MongoDB\Id(strategy="auto")
         */
        protected $id;
    }

c) CouchDB Group class implementation
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    // src/MyProject/MyBundle/CouchDocument/Group.php

    namespace MyProject\MyBundle\CouchDocument;

    use FOS\UserBundle\Model\Group as BaseGroup;
    use Doctrine\ODM\CouchDB\Mapping\Annotations as CouchDB;

    /**
     * @CouchDB\Document
     */
    class Group extends BaseGroup
    {
        /**
         * @CouchDB\Id
         */
        protected $id;
    }

Defining the User-Group relation
--------------------------------

The next step is to map the relation in your ``User`` class.

a) ORM User-Group mapping
~~~~~~~~~~~~~~~~~~~~~~~~~

.. configuration-block::

    .. code-block:: php-annotations

        // src/MyProject/MyBundle/Entity/User.php

        namespace MyProject\MyBundle\Entity;

        use FOS\UserBundle\Model\User as BaseUser;

        /**
         * @ORM\Entity
         * @ORM\Table(name="fos_user")
         */
        class User extends BaseUser
        {
            /**
             * @ORM\Id
             * @ORM\Column(type="integer")
             * @ORM\GeneratedValue(strategy="AUTO")
             */
            protected $id;

            /**
             * @ORM\ManyToMany(targetEntity="MyProject\MyBundle\Entity\Group")
             * @ORM\JoinTable(name="fos_user_user_group",
             *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
             *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
             * )
             */
            protected $groups;
        }

    .. code-block:: yaml

        # src/AppBundle/Resources/config/doctrine/User.orm.yml
        AppBundle\Entity\User:
            type:  entity
            table: fos_user
            id:
                id:
                    type: integer
                    generator:
                        strategy: AUTO
            manyToMany:
                groups:
                    targetEntity: Group
                    joinTable:
                        name: fos_user_group
                        joinColumns:
                            user_id:
                                referencedColumnName: id
                        inverseJoinColumns:
                            group_id:
                                referencedColumnName: id

    .. code-block:: xml

        <?xml version="1.0" encoding="UTF-8"?>
        <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                          xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                          xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                          http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">
            <entity name="AppBundle\Entity\User" table="fos_user">
                <id name="id" column="id" type="integer">
                    <generator strategy="AUTO" />
                </id>
                <many-to-many field="groups" target-entity="Group">
                    <join-table name="fos_user_group">
                        <join-columns>
                            <join-column name="user_id" referenced-column-name="id"/>
                        </join-columns>
                        <inverse-join-columns>
                            <join-column name="group_id" referenced-column-name="id" />
                        </inverse-join-columns>
                    </join-table>
                </many-to-many>
            </entity>
        </doctrine-mapping>

b) MongoDB User-Group mapping
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    // src/MyProject/MyBundle/Document/User.php

    namespace MyProject\MyBundle\Document;

    use FOS\UserBundle\Model\User as BaseUser;
    use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

    /**
     * @MongoDB\Document
     */
    class User extends BaseUser
    {
        /** @MongoDB\Id(strategy="auto") */
        protected $id;

        /**
         * @MongoDB\ReferenceMany(targetDocument="MyProject\MyBundle\Document\Group")
         */
        protected $groups;
    }

c) CouchDB User-Group mapping
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    // src/MyProject/MyBundle/CouchDocument/User.php

    namespace MyProject\MyBundle\CouchDocument;

    use FOS\UserBundle\Model\User as BaseUser;
    use Doctrine\ODM\CouchDB\Mapping\Annotations as CouchDB;

    /**
     * @CouchDB\Document
     */
    class User extends BaseUser
    {
        /**
         * @CouchDB\Id
         */
        protected $id;

        /**
         * @CouchDB\ReferenceMany(targetDocument="MyProject\MyBundle\CouchDocument\Group")
         */
        protected $groups;
    }

Enabling the routing for the GroupController
--------------------------------------------

You can import the routing file ``group.xml`` to use the built-in controller to
manipulate groups.

.. code-block:: yaml

    # app/config/routing.yml
    fos_user_group:
        resource: "@FOSUserBundle/Resources/config/routing/group.xml"
        prefix: /group
