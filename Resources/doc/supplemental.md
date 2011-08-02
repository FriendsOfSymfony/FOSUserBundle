Using Groups With FOSUserBundle
===============================

The FOSUserBundle allows you to optionally use groups. You need to explicitly
enable this functionality in your configuration by specifying the fully
qualified class name (FQCN) of your `Group` class which must implement
`FOS\UserBundle\Model\GroupInterface`.

Below is an example configuration for enabling groups support.

In YAML:

``` yaml
# app/config/config.yml
fos_user:
    db_driver: orm
    firewall_name: main
    user_class: Acme\UserBundle\Entity\User
    group:
        group_class: Acme\UserBundle\Entity\Group
```

Or if you prefer XML:

``` xml
# app/config/config.xml
<fos_user:config
    db-driver="orm"
    firewall-name="main"
    user-class="Acme\UserBundle\Entity\User"
>
    <fos_user:group group-class model="Acme\UserBundle\Entity\Group" />
</fos_user:config>
```
### The Group class

The simpliest way to create a Group class is to extend the mapped superclass
provided by the bundle.

**a) ORM Group class implementation**

``` php
// src/MyProject/MyBundle/Entity/Group.php
<?php

namespace MyProject\MyBundle\Entity;

use FOS\UserBundle\Entity\Group as BaseGroup;
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
     * @ORM\generatedValue(strategy="AUTO")
     */
     protected $id;
}
```

**Note:** `Group` is a reserved keyword in SQL so it cannot be used as the table name.

**b) MongoDB Group class implementation**

``` php
// src/MyProject/MyBundle/Document/Group.php
<?php

namespace MyProject\MyBundle\Document;

use FOS\UserBundle\Document\Group as BaseGroup;
use Doctrine\ODM\MongoDB\Mapping as MongoDB;

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
```

**c) CouchDB Group class implementation**

``` php
// src/MyProject/MyBundle/Document/Group.php
<?php

namespace MyProject\MyBundle\Document;

use FOS\UserBundle\Document\Group as BaseGroup;
use Doctrine\ODM\CouchDB\Mapping as MongoDB;

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
```

### Defining the User-Group relation

The next step is to map the relation in your `User` class.

**a) ORM User-Group mapping**

``` php
// src/MyProject/MyBundle/Entity/User.php
<?php

namespace MyProject\MyBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
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
     * @ORM\generatedValue(strategy="AUTO")
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
```

**b) MongoDB User-Group mapping**

``` php
// src/MyProject/MyBundle/Document/User.php
<?php

namespace MyProject\MyBundle\Document;

use FOS\UserBundle\Document\User as BaseUser;
use Doctrine\ODM\MongoDB\Mapping as MongoDB;

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
```

**c) CouchDB User-Group mapping**

``` php
// src/MyProject/MyBundle/Document/User.php
<?php

namespace MyProject\MyBundle\Document;

use FOS\UserBundle\Document\User as BaseUser;
use Doctrine\ODM\CouchDB\Mapping as CouchDB;

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
     * @CouchDB\ReferenceMany(targetDocument="MyProject\MyBundle\Document\Group")
     */
    protected $groups;
}
```

### Enabling the routing for the GroupController

You can import the routing file `group.xml` to use the built-in controller to
manipulate groups.

In YAML:

``` yaml
# app/config/routing.yml
fos_user_group:
    resource: "@FOSUserBundle/Resources/config/routing/group.xml"
    prefix: /group

```

About FOSUserBundle User Manager Service
========================================

FOSUserBundle works with both ORM and ODM. To make this possible, it wraps
all the operation on users in a UserManager. The user manager is configured
as a service in the container.

If you configure the db_driver to `orm`, this service is an instance of
`FOS\UserBundle\Entity\UserManager`.

If you configure the db_driver to `mongodb`, this service is an instance of
`FOS\UserBundle\Document\UserManager`.

If you configure the db_driver to `couchdb`, this service is an instance of
`FOS\UserBundle\CouchDocument\UserManager`.

All these classes implement `FOS\UserBundle\Model\UserManagerInterface`.

### Access the User Manager service

If you want to manipulate users in a way that will work as well with
ORM and ODM, use the `fos_user.user_manager` service.

``` php
$userManager = $container->get('fos_user.user_manager');
```

That's the way FOSUserBundle's internal controllers are built.

## Create a new User

A new instance of your User class can be created by the user manager.

``` php
$user = $userManager->createUser();
```

`$user` is now an Entity or a Document, depending on the configuration.

### Updating a User object

When creating or updating a User object you need to update the encoded password
and the canonical fields. To make it easier, the bundle comes with a Doctrine
listener handling this for you behind the scenes.

If you don't want to use the Doctrine listener, you can disable it. In this case
you will have to call the `updateUser` method of the user manager each time
you make a change to your entity.

In YAML:

``` yaml
# app/config/config.yml
fos_user:
    # ...
    user_class: MyProject\MyBundle\Entity\User
```

Or if you prefer XML:

``` xml
# app/config/config.xml
<fos_user:config
    db-driver="orm"
    firewall-name="main"
    use-listener="false"
    user-class="MyProject\MyBundle\Entity\User"
/>
```

The default behavior is to flush the changes when calling the `updateUser` method.
You can disable the flush when using the ORM and the MongoDB implementations by
passing a second argument set to `false`.

An ORM example:

``` php
public function MainController extends Controller
{
    public function updateAction($id)
    {
        $user = // get a user from the datastore

        $user->setEmail($newEmail);

        $this->get('fos_user.user_manager')->udpateUser($user, false);

        // make more modifications to the database

        $this->getDoctrine()->getEntityManager()->flush();
    }
}
```

Overriding Default User Manager
===============================

You can replace the default implementation of the user manager by defining
a service implementing `FOS\UserBundle\Model\UserManagerInterface` and
setting its id in the configuration.

In YAML:

``` yaml
fos_user:
    # ...
    service:
        user_manager: custom_user_manager_id
```

Overriding Default FOSUserBundle Validation
===========================================

The `Resources/config/validation.xml` file contains definitions for custom
validator rules for various classes. The rules defined by FOSUserBundle are
all in validation groups so you can choose not to use them.

Form Types
==========

## The username Form Type

The bundle also provides a convenient username form type.
It appears as a text input, accepts usernames and convert them to a User instance.

You can enable this feature from the configuration.

In YAML:

``` yaml
# app/config/config.yml
fos_user:
    use_username_form_type: true
```

And then use it in your forms.

``` php
class MessageFormType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('recipient', 'fos_user_username');
    }
```

FOSUserBundle Emails
====================

The default mailer relies on Swiftmailer to send the mails of the bundle.
If you want to use another mailer in your project you can change it by defining
your own service implementing `FOS\UserBundle\Mailer\MailerInterface` and
setting its id in the configuration.

In YAML:

``` yaml
fos_user:
    # ...
    service:
        mailer: custom_mailer_id
```

This bundle comes with two mailer implementations.

- `fos_user.mailer.default` is the default implementation, and uses Swiftmailer to send emails.
- `fos_user.mailer.noop` does nothing and can be used if your project does not depend on Swiftmailer.

FOSUserBundle Canonicalization
==============================

`Canonicalizer` services are used to canonicalize the username and the email
fields for database storage. By default, username and email fields are
canonicalized in the same manner using `mb_convert_case()`. You may configure
your own class for each field provided it implements
`FOS\UserBundle\Util\CanonicalizerInterface`.

**Note:**

```
If you do not have the mbstring extension installed you will need to define your
own `canonicalizer`.
```