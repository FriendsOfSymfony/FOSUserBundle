Provides user persistence for your Symfony2 Project.

Features
========

- Compatible with Doctrine ORM **and** ODM thanks to a generic repository.
- Model is extensible at will
- REST-ful authentication
- Current user available in your controllers and views
- Unit tested and functionally tested

Warning
=======

The supplied Controller and routing configuration files expose as much functionality as possible to illustrate how to use the Bundle. However using these exposes a lot of functionality which requires additional configuration to secure properly. As such its not recommended to ever go into production while using one of the default routing configuration files. Furthermore it may be necessary to extend or even replace the default Controllers with custom code to achieve the exact desired behavior. Trying to cover every possible use case is not feasible as it would complicate the Bundle to the point of being unmaintainable and impossible to comprehend in a reasonable amount of time.

Installation
============

Add UserBundle to your vendor/bundles/ dir
------------------------------------------

::

    $ git submodule add git://github.com/FriendsOfSymfony/UserBundle.git vendor/bundles/FOS/UserBundle

Add the FOS namespace to your autoloader
----------------------------------------

::

    // app/autoload.php
    $loader->registerNamespaces(array(
        'FOS' => __DIR__.'/../vendor/bundles',
        // your other namespaces
    );

Add UserBundle to your application kernel
-----------------------------------------

::

    // app/AppKernel.php

    public function registerBundles()
    {
        return array(
            // ...
            new FOS\UserBundle\FOSUserBundle(),
            // ...
        );
    }

Create your User class
----------------------

You must create a User class that extends either the entity or document abstract
User class in UserBundle.  All fields on the base class are mapped, except for
``id``; this is intentional, so you can select the generator that best suits
your application. Feel free to add additional properties and methods to your
custom class.

ORM User class
~~~~~~~~~~~~~~

::

    // src/MyProject/MyBundle/Entity/User.php

    namespace MyProject\MyBundle\Entity;
    use FOS\UserBundle\Entity\User as BaseUser;

    /**
     * @orm:Entity
     */
    class User extends BaseUser
    {
        /**
         * @orm:Id
         * @orm:Column(type="integer")
         * @orm:generatedValue(strategy="AUTO")
         */
        protected $id;

        public function __construct()
        {
            parent::__construct();
            // your own logic
        }
    }

MongoDB User class
~~~~~~~~~~~~~~~~~~

::

    // src/MyProject/MyBundle/Document/User.php

    namespace MyProject\MyBundle\Document;
    use FOS\UserBundle\Document\User as BaseUser;

    /**
     * @mongodb:Document
     */
    class User extends BaseUser
    {
        /** @mongodb:Id(strategy="auto") */
        protected $id;

        public function __construct()
        {
            parent::__construct();
            // your own logic
        }
    }

.. warning::

    Take care to call the parent constructor when you overwrite it in your own
    entity as it initializes some fields.

Configure your project
----------------------

The UserBundle works with the Symfony Security Component, so make sure that is
enabled in your kernel and in your project's configuration::

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            // ...
        );
    }

    # app/config/security.yml
    security:
        providers:
            # the naming of a security provider is up to you, we chose "fos_userbundle"
            fos_userbundle:
                id: fos_user.user_manager

.. note::

    You need to activate SwiftmailerBundle to be able to use the functionalities
    using emails (confirmation of the account, resetting of the password).
    See the `Emails` section to know how using another mailer.

The login form and all the routes used to create a user and reset the password
have to be available to unauthenticated users but using the same firewall as
the pages you want to securize with the bundle. Assuming you import the
user.xml routing file with the ``/user`` prefix they will be::

    /login
    /user/new
    /user/check-confirmation-email
    /user/confirm/{token}
    /user/confirmed
    /user/request-reset-password
    /user/send-resetting-email
    /user/check-resetting-email
    /user/reset-password/{token}

.. note::

    You can look at the end of the doc for a working security configuration
    achieving this.

You also have to include the UserBundle in your Doctrine mapping configuration,
along with the bundle containing your custom User class::

    # app/config/config.yml
    doctrine:
        orm:
            mappings:
                FOSUserBundle: ~
                MyProjectMyBundle:   ~
                # your other bundles

The above example assumes an ORM configuration, but the ``mappings``
configuration block would be the same for MongoDB ODM.

Minimal configuration
---------------------

At a minimum, your configuration must define your DB driver ("orm" or "mongodb"),
a User class and the provider key. The provider key matches the key in the
firewall configuration that is used for users with the UserController.

The provider key needs to be configured so that the UserBundle can determine
against what firewall the user should be authenticated after activating the
account for example. This means that out of the box UserBundle only supports
being used for a single firewall, though with a custom Controller this
limitation can be circumvented.

For example for a security configuration like the following the firewall_name
would have to be set to "main", as shown in the proceeding examples:

::

    # app/config/config.yml
    security:
        providers:
            fos_userbundle:
                id: fos_user.user_manager

        firewalls:
            main:
                form_login:
                    provider: fos_userbundle

ORM
~~~

In YAML:

::

    # app/config/config.yml
    fos_user:
        db_driver: orm
        firewall_name: main
        class:
            model:
                user: MyProject\MyBundle\Entity\User

Or if you prefer XML:

::

    # app/config/config.xml

    <fos_user:config db-driver="orm" firewall-name="main">
        <fos_user:class>
            <fos_user:model
                user="MyProject\MyBundle\Entity\User"
            />
        </fos_user:class>
    </fos_user:config>

ODM
~~~

In YAML:

::

    # app/config/config.yml
    fos_user:
        db_driver: mongodb
        firewall_name: main
        class:
            model:
                user: MyProject\MyBundle\Document\User

Or if you prefer XML:

::

    # app/config/config.xml

    <fos_user:config db-driver="mongodb" firewall-name="main">
        <fos_user:class>
            <fos_user:model
                user="MyProject\MyBundle\Document\User"
            />
        </fos_user:class>
    </fos_user:config>


Add authentication routes
-------------------------

If you want ready to use login and logout pages, include the built-in
routes:

::

    # app/config/routing.yml
    fos_user_security:
        resource: "@FOSUserBundle/Resources/config/routing/security.xml"

    fos_user_user:
        resource: "@FOSUserBundle/Resources/config/routing/user.xml"
        prefix: /user

::

    # app/config/routing.xml

    <import resource="@FOSUserBundle/Resources/config/routing/security.xml"/>
    <import resource="@FOSUserBundle/Resources/config/routing/user.xml" prefix="/user" />

You now can login at http://app.com/login

Command line
============

UserBundle provides command line utilities to help manage your
application users.

Create user
-----------

This command creates a new user::

    $ php app/console fos:user:create username email password

If you don't provide the required arguments, a interactive prompt will
ask them to you::

    $ php app/console fos:user:create

Promote user as a super administrator
-------------------------------------

This command promotes a user as a super administrator::

    $ php app/console fos:user:promote

User manager service
====================

UserBundle works with both ORM and ODM. To make it possible, it wraps
all the operation on users in a UserManager. The user manager is a
service of the container.

If you configure the db_driver to orm, this service is an instance of
``FOS\UserBundle\Entity\UserManager``.

If you configure the db_driver to odm, this service is an instance of
``FOS\UserBundle\Document\UserManager``.

Both these classes implement ``FOS\UserBundle\Model\UserManagerInterface``.

Access the user manager service
-------------------------------

If you want to manipulate users in a way that will work as well with
ORM and ODM, use the fos_user.user_manager service::

    $userManager = $container->get('fos_user.user_manager');

That's the way UserBundle's internal controllers are built.

Access the current user class
-----------------------------

A new instance of your User class can be created by the user manager::

    $user = $userManager->createUser();

`$user` is now an Entity or a Document, depending on the configuration.

Updating a User object
----------------------

When creating or updating a User object you need to update the encoded password
and the canonical fields. To make it easier, the bundle comes with a Doctrine
listener handling this for you behind the scene.

If you don't want to use the Doctrine listener, you can disable it. In this case
you will have to call the ``updateUser`` method of the user manager each time
you do a change in your entity.

In YAML:

::

    # app/config/config.yml
    fos_user:
        db_driver: orm
        firewall_name: main
        use_listener: false
        class:
            model:
                user: MyProject\MyBundle\Entity\User

Or if you prefer XML:

::

    # app/config/config.xml

    <fos_user:config db-driver="orm" firewall-name="main" use-listener="false">
        <fos_user:class>
            <fos_user:model
                user="MyProject\MyBundle\Entity\User"
            />
        </fos_user:class>
    </fos_user:config>

.. note::

    The default behavior is to flush the changes when calling this method. You
    can disable the flush when using the ORM and the MongoDB implementations by
    passing a second argument set to ``false``.

Using groups
============

The bundle allows to optionnally use groups. You need to explicitly
enable it in your configuration by giving the Group class which must
implement ``FOS\UserBundle\Model\GroupInterface``.

In YAML:

::

    # app/config/config.yml
    fos_user:
        db_driver: orm
        firewall_name: main
        class:
            model:
                user: MyProject\MyBundle\Entity\User
        group:
            class:
                model: MyProject\MyBundle\Entity\Group

Or if you prefer XML:

::

    # app/config/config.xml

    <fos_user:config db-driver="orm" firewall-name="main">
        <fos_user:class>
            <fos_user:model
                user="MyProject\MyBundle\Entity\User"
            />
        </fos_user:class>
        <fos_user:group>
            <fos_user:class model="MyProject\MyBundle\Entity\Group" />
        </fos_user:group>
    </fos_user:config>

The Group class
---------------

The simpliest way is to extend the mapped superclass provided by the
bundle.

ORM
~~~

::

    // src/MyProject/MyBundle/Entity/UserGroups.php

    namespace MyProject\MyBundle\Entity;
    use FOS\UserBundle\Entity\Group as BaseGroup;

    /**
     * @orm:Entity
     * @orm:Column(name="user_groups")
     */
    class UserGroups extends BaseGroup
    {
        /**
         * @orm:Id
         * @orm:Column(type="integer")
         * @orm:generatedValue(strategy="AUTO")
         */
        protected $id;
    }

Note: This class cannot be named as "Group" because it is a mysql reserved word.

ODM
~~~

::

    // src/MyProject/MyBundle/Document/Group.php

    namespace MyProject\MyBundle\Document;
    use FOS\UserBundle\Document\Group as BaseGroup;

    /**
     * @mongodb:Document
     */
    class Group extends BaseGroup
    {
        /** @mongodb:Id(strategy="auto") */
        protected $id;
    }

Defining the relation
---------------------

The next step is to map the relation in your User class.

ORM
~~~

::

    // src/MyProject/MyBundle/Entity/User.php

    namespace MyProject\MyBundle\Entity;
    use FOS\UserBundle\Entity\User as BaseUser;

    /**
     * @orm:Entity
     */
    class User extends BaseUser
    {
        /**
         * @orm:Id
         * @orm:Column(type="integer")
         * @orm:generatedValue(strategy="AUTO")
         */
        protected $id;

        /**
         * @orm:ManyToMany(targetEntity="MyProject\MyBundle\Entity\Group")
         * @orm:JoinTable(name="fos_user_user_group",
         *      joinColumns={@orm:JoinColumn(name="user_id", referencedColumnName="id")},
         *      inverseJoinColumns={@orm:JoinColumn(name="group_id", referencedColumnName="id")}
         * )
         */
        protected $groups;
    }

ODM
~~~

::

    // src/MyProject/MyBundle/Document/User.php

    namespace MyProject\MyBundle\Document;
    use FOS\UserBundle\Document\User as BaseUser;

    /**
     * @mongodb:Document
     */
    class User extends BaseUser
    {
        /** @mongodb:Id(strategy="auto") */
        protected $id;

        /** @mongodb:ReferenceMany(targetDocument="MyProject\MyBundle\Document\Group") */
        protected $groups;
    }

Enabling the routing for the GroupController
--------------------------------------------

You can also the group.xml file to use the builtin controller to manipulate the
groups.

Configuration reference
=======================

All configuration options are listed below::

    # app/config/config.yml
    fos_user:
        db_driver:     mongodb
        firewall_name: main
        use_listener:  true
        class:
            model:
                user:  MyProject\MyBundle\Document\User
            form:
                user:            ~
                change_password: ~
                reset_password:  ~
            controller:
                user:     ~
                security: ~
        service:
            mailer: ~
            email_canonicalizer:    ~
            username_canonicalizer: ~
        encoder:
            algorithm:        ~
            encode_as_base64: ~
            iterations:       ~
        form_name:
            user:            ~
            change_password: ~
            reset_password:  ~
        form_validation_groups:
            user: ~             # This value is an array of groups
        email:
            from_email: ~       # { admin@example.com: Sender_name }
            confirmation:
                enabled:    ~
                template:   ~
            resetting_password:
                template:   ~
                token_ttl:  ~
        template:
            engine: ~
            theme:  ~
        group:
            class:
                model: MyProject\MyBundle\Document\Group
                controller: ~
                form: ~
            form_name: ~
            form_validation_groups: ~

Configuration example
=====================

This section provides a working configuration for the bundle and the security.

FOSUserBundle configuration
---------------------------

::

    # app/config/config.yml
    fos_user:
        db_driver:     orm
        firewall_name: main
        class:
            model:
                user:  MyProject\MyBundle\Entity\User

Security configuration
----------------------

::

    # app/config/security.yml
    security:
        providers:
            fos_userbundle:
                id: fos_user.user_manager

        firewalls:
            main:
                pattern:      .*
                form-login:
                    provider:       fos_userbundle
                    login_path:     /login
                    use_forward:    false
                    check_path:     /login_check
                    failure_path:   null
                logout:       true
                anonymous:    true

        access_control:
            # The WDT has to be allowed to anonymous users to avoid requiring the login with the AJAX request
            - { path: ^/_wdt/, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/_profiler/, role: IS_AUTHENTICATED_ANONYMOUSLY }
            # AsseticBundle paths used when using the controller for assets
            - { path: ^/js/, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/css/, role: IS_AUTHENTICATED_ANONYMOUSLY }
            # URL of FOSUserBundle which need to be available to anonymous users
            - { path: ^/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/login_check$, role: IS_AUTHENTICATED_ANONYMOUSLY } # for the case of a failed login
            - { path: ^/user/new$, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/user/check-confirmation-email$, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/user/confirm/, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/user/confirmed$, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/user/request-reset-password$, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/user/send-resetting-email$, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/user/check-resetting-email$, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/user/reset-password/, role: IS_AUTHENTICATED_ANONYMOUSLY }
            # Secured part of the site
            # This config requires being logged for the whole site and having the admin role for the admin part.
            # Change these rules to adapt them to your needs
            - { path: ^/admin/, role: ROLE_ADMIN }
            - { path: ^/.*, role: ROLE_USER }

        role_hierarchy:
            ROLE_ADMIN:       ROLE_USER
            ROLE_SUPERADMIN:  ROLE_ADMIN

Replacing some part by your own implementation
==============================================

Templating
----------

The template names are not configurable, however Symfony2 makes it possible
to extend a bundle by defining a template in the app/ directory.

For example ``vendor/bundles/FOS/UserBundle/Resources/views/User/new.twig`` can be
replaced inside an application by putting a file with alternative content in
``app/Resources/FOSUserBundle/views/User/new.twig``.

You could also create a bundle defined as child of FOSUserBundle and placing the
templates in it.

You can use a different templating engine by configuring it but you will have to
create all the needed templates as only twig templates are provided.

Validation
----------

The ``Resources/config/validation.xml`` file contains definitions for custom
validator rules for various classes. The rules for the ``User`` class are all in
the ``Registration`` validation group so you can choose not to use them.

Emails
------

The default mailer relies on Swiftmailer to send the mails of the bundle. If you
want to use another mailer in your project you can change it by defining your
own service implementing ``FOS\UserBundle\Mailer\MailerInterface`` and setting its
id in the configuration::

    fos_user:
        # ...
        service:
            mailer: custom_mailer_id

This bundle comes with two mailer implementations.

- `fos_user.mailer.default` is the default implementation, and uses swiftmailer to send emails.
- `fos_user.mailer.noop` does nothing and can be used if your project does not depend on swiftmailer.

Canonicalization
----------------

``Canonicalizer`` services are used to canonicalize the username and the email
fields for database storage. By default, username and email fields are
canonicalized in the same manner using ``mb_convert_case()``. You may configure
your own class for each field provided it implements
``FOS\UserBundle\Util\CanonicalizerInterface``.

.. note::

    If you do not have the mbstring extension installed you will need to
    define your own ``canonicalizer``.
