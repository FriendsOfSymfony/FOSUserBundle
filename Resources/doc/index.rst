Getting Started With FOSUserBundle
==================================

The Symfony Security component provides a flexible security framework that
allows you to load users from configuration, a database, or anywhere else
you can imagine. The FOSUserBundle builds on top of this to make it quick
and easy to store users in a database, as well as functionality for registration,
reset password and a profile page.

So, if you need to persist and fetch the users in your system to and from
a database, then you're in the right place.

For a video tutorial, check out `FOSUserBundle FTW`_ by KnpUniversity.

Prerequisites
-------------

This version of the documentation requires Symfony flex. If you are using an older
Symfony version, please use the 2.0.x or 1.3.x releases of the documentation.

Translations
~~~~~~~~~~~~

If you wish to use default texts provided in this bundle, you have to make
sure you have translator installed in your project.

.. code-block:: bash

    $ composer require symfony/translation

For more information about translations, check `translator component documentation`_.

Mailer
~~~~~~

This bundle requires a mailer to send email provided in this bundle, you have to make
sure you have a mailer intalled in your project.

.. code-block:: bash

    $ composer require symfony/swiftmailer-bundle

For more information about email, check `swiftmailer documentation`_.

Storage
~~~~~~~

This bundle requires a storage to persist some ``User`` class to a database.
If you wish to use one of the doctrine variants (Doctrine ORM, MongoDB ODM,
or CouchDB ODM). You also may use a custom storage.

.. code-block:: bash

    $ composer require symfony/orm-pack
    $ composer require doctrine/mongodb-odm-bundle
    $ composer require doctrine/couchdb-odm-bundle


Installation
------------

Installation is a quick (I promise!) 6 step process:

1. Download FOSUserBundle using composer
2. Configure the FOSUserBundle
3. Create your User class
4. Configure your application's security.yaml
5. Import FOSUserBundle routing
6. Update your database schema


Step 1: Download FOSUserBundle using composer
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Require the bundle with composer:

.. code-block:: bash

    $ composer require friendsofsymfony/user-bundle "~2.1"

Composer will install the bundle to your project's ``vendor/friendsofsymfony/user-bundle`` directory.
If you encounter installation errors pointing at a lack of configuration parameters,
such as ``The child node "db_driver" at path "fos_user" must be configured``,
you should complete the configuration in Step 2 first and then re-run this step.

The bundle enabling should be done automatically in the ``config/bundles.php`` by the auto generated recipe.


Step 2: Configure the FOSUserBundle
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The next step is to configure the bundle to work with the specific needs of your application.

Add the following configuration to your ``config/packages/fos_user.yaml`` file
according to which type of datastore you are using. You should create the file if needed.

.. configuration-block::

    .. code-block:: yaml

        # config/packages/fos_user.yaml

        framework:
            csrf_protection: true
            templating:
                engines: ['twig']

        fos_user:
            db_driver: orm # other valid values are 'mongodb' and 'couchdb'
            firewall_name: main
            user_class: App\Entity\User
            from_email:
                address: "%env(MAILER_SENDER_ADDRESS)%"
                sender_name: "%env(MAILER_SENDER_NAME)%"

    .. code-block:: xml

        <!-- config/packages/fos_user.xml -->

        <framework:config>
            <framework:config csrf-protection />
            <framework:templating>
                <framework:engines>twig</framework:engines>
            </framework:templating>
        </framework:config>

        <!-- other valid 'db-driver' values are 'mongodb' and 'couchdb' -->
        <fos_user:config
            db-driver="orm"
            firewall-name="main"
            user-class="App\Entity\User"
        />

Only four configuration's nodes are required to use the bundle:

* The type of datastore you are using (``orm``, ``mongodb`` or ``couchdb``).
* The firewall name which you configured in 5.
* The fully qualified class name (FQCN) of the ``User`` class which you created in Step 3.
* The default email address to use when the bundle send a registration confirmation to the user.

You should add the following environnements variables to your ``.env`` file

.. code-block:: env

    MAILER_SENDER_ADDRESS=johndoe@example.com
    MAILER_SENDER_NAME="John Doe"

.. note::

    FOSUserBundle uses a compiler pass to register mappings for the base
    User and Group model classes with the object manager that you configured
    it to use. (Unless specified explicitly, this is the default manager
    of your doctrine configuration.)


Step 3: Create your User class
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The goal of this bundle is to persist some ``User`` class to a database (MySql,
MongoDB, CouchDB, etc). Your first job, then, is to create the ``User`` class
for your application. This class can look and act however you want: add any
properties or methods you find useful. This is *your* ``User`` class.

The bundle provides base classes which are already mapped for most fields
to make it easier to create your entity. Here is how you use it:

1. Extend the base ``User`` class (from the ``Model`` folder if you are using
   any of the doctrine variants)
2. Map the ``id`` field. It must be protected as it is inherited from the parent class.

.. caution::

    When you extend from the mapped superclass provided by the bundle, don't
    redefine the mapping for the other fields as it is provided by the bundle.

In the following sections, you'll see examples of how your ``User`` class should
look, depending on how you're storing your users (Doctrine ORM, MongoDB ODM,
or CouchDB ODM).

.. note::

    The doc uses a bundle named ``App`` according to the Symfony best
    practices. However, you can of course place your user class in the bundle
    you want.

.. caution::

    If you override the __construct() method in your User class, be sure
    to call parent::__construct(), as the base User class depends on
    this to initialize some fields.

a) Doctrine ORM User class
..........................

If you're persisting your users via the Doctrine ORM, then your ``User`` class
should live in the ``Entity`` namespace of your bundle and look like this to
start:

.. configuration-block::

    .. code-block:: php-annotations

        <?php
        // src/Entity/User.php

        namespace App\Entity;

        use FOS\UserBundle\Model\User as BaseUser;
        use Doctrine\ORM\Mapping as ORM;

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

            public function __construct()
            {
                parent::__construct();
                // your own logic
            }
        }

    .. code-block:: yaml

        # src/Entity/User.orm.yaml
        App\Entity\User:
            type:  entity
            table: fos_user
            id:
                id:
                    type: integer
                    generator:
                        strategy: AUTO

    .. code-block:: xml

        <?xml version="1.0" encoding="utf-8"?>
        <!-- src/Entity/User.orm.xml -->
        <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

            <entity name="App\Entity\User" table="fos_user">
                <id name="id" type="integer" column="id">
                    <generator strategy="AUTO"/>
                </id>
            </entity>
        </doctrine-mapping>

.. caution::

    ``user`` is a reserved keyword in the SQL standard. If you need to use reserved words, surround them with backticks, *e.g.* ``@ORM\Table(name="`user`")``

b) MongoDB User class
.....................

If you're persisting your users via the Doctrine MongoDB ODM, then your ``User``
class should live in the ``Document`` namespace of your bundle and look like
this to start::

    <?php
    // src/Document/User.php

    namespace App\Document;

    use FOS\UserBundle\Model\User as BaseUser;
    use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

    /**
     * @MongoDB\Document
     */
    class User extends BaseUser
    {
        /**
         * @MongoDB\Id(strategy="auto")
         */
        protected $id;

        public function __construct()
        {
            parent::__construct();
            // your own logic
        }
    }

c) CouchDB User class
.....................

If you're persisting your users via the Doctrine CouchDB ODM, then your ``User``
class should live in the ``CouchDocument`` namespace of your bundle and look
like this to start::

    <?php
    // src/CouchDocument/User.php

    namespace App\CouchDocument;

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

        public function __construct()
        {
            parent::__construct();
            // your own logic
        }
    }


5: Configure your application's security.yaml
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

In order for Symfony's security component to use the FOSUserBundle, you must
tell it to do so in the ``config/packages/security.yaml`` file.
The ``security.yaml`` file is where the basic security configuration for your application is contained.

Below is a minimal example of the configuration necessary to use the FOSUserBundle
in your application:

.. code-block:: yaml

    # config/packages/security.yaml
    security:
        encoders:
            FOS\UserBundle\Model\UserInterface: bcrypt

        role_hierarchy:
            ROLE_ADMIN:       ROLE_USER
            ROLE_SUPER_ADMIN: ROLE_ADMIN

        providers:
            fos_userbundle:
                id: fos_user.user_provider.username

        firewalls:
            main:
                pattern: ^/
                user_checker: fos_user.user_checker
                form_login:
                    provider: fos_userbundle
                    csrf_token_generator: security.csrf.token_manager

                logout:       true
                anonymous:    true

        access_control:
            - { path: ^/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/register, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/resetting, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/admin/, role: ROLE_ADMIN }

Under the ``providers`` section, you are making the bundle's packaged user provider
service available via the alias ``fos_userbundle``. The id of the bundle's user
provider service is ``fos_user.user_provider.username``.

Next, take a look at and examine the ``firewalls`` section. Here we have
declared a firewall named ``main``. By specifying ``form_login``, you have
told the Symfony Framework that any time a request is made to this firewall
that leads to the user needing to authenticate himself, the user will be
redirected to a form where he will be able to enter his credentials. It should
come as no surprise then that you have specified the user provider service
we declared earlier as the provider for the firewall to use as part of the
authentication process.

.. note::

    Although we have used the form login mechanism in this example, the FOSUserBundle
    user provider service is compatible with many other authentication methods
    as well. Please read the Symfony Security component documentation for
    more information on the other types of authentication methods.

The ``access_control`` section is where you specify the credentials necessary for
users trying to access specific parts of your application. The bundle requires
that the login form and all the routes used to create a user and reset the password
be available to unauthenticated users but use the same firewall as
the pages you want to secure with the bundle. This is why you have specified that
any request matching the ``/login`` pattern or starting with ``/register`` or
``/resetting`` have been made available to anonymous users. You have also specified
that any request beginning with ``/admin`` will require a user to have the
``ROLE_ADMIN`` role.

For more information on configuring the ``security.yaml`` file please read the Symfony
`security component documentation`_.

.. note::

    Pay close attention to the name, ``main``, that we have given to the
    firewall which the FOSUserBundle is configured in. You will use this
    in the ``fos_user.yaml`` configuration file.


Step 5: Import FOSUserBundle routing files
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Now that you have activated and configured the bundle, all that is left to do is
import the FOSUserBundle routing files.

By importing the routing files you will have ready made pages for things such as
logging in, creating users, etc.

You should create the ``config/routes/fos_user.yaml`` if needed.

.. configuration-block::

    .. code-block:: yaml

        # config/routes/fos_user.yaml
        fos_user:
            resource: "@FOSUserBundle/Resources/config/routing/all.xml"

    .. code-block:: xml

        <!-- config/routes/fos_user.xml -->
        <import resource="@FOSUserBundle/Resources/config/routing/all.xml"/>

.. note::

    In order to use the built-in email functionality (confirmation of the account,
    resetting of the password), you must activate and configure the SwiftmailerBundle.


Step 6: Update your database schema
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Now that the bundle is configured, the last thing you need to do is update your
database schema because you have added a new entity, the ``User`` class which you
created in Step 3.

For ORM run the following command.

.. code-block:: bash

    $ php bin/console doctrine:migration:diff
    $ php bin/console doctrine:migration:migrate --no-interaction

For MongoDB users you can run the following command to create the indexes.

.. code-block:: bash

    $ php bin/console doctrine:mongodb:schema:create --index

You now can log in at ``http://localhost:8000/login``!

Next Steps
~~~~~~~~~~

Now that you have completed the basic installation and configuration of the
FOSUserBundle, you are ready to learn about more advanced features and usages
of the bundle.

The following documents are available:

.. toctree::
    :maxdepth: 1

    overriding_templates
    controller_events
    overriding_forms
    user_manager
    command_line_tools
    logging_by_username_or_email
    form_type
    emails
    groups
    doctrine
    overriding_validation
    canonicalizer
    custom_storage_layer
    routing
    configuration_reference
    adding_invitation_registration

.. _security component documentation: https://symfony.com/doc/current/security.html
.. _translator component documentation: https://symfony.com/doc/current/translation.html
.. _swiftmailer documentation: https://symfony.com/doc/current/email.html
.. _TypehintableBehavior: https://github.com/willdurand/TypehintableBehavior
.. _FOSUserBundle FTW: https://knpuniversity.com/screencast/fosuserbundle
