Using Groups With FOSUserBundle
===============================

FOSUserBundle allows you to associate groups to your users. Groups are a
way to group a collection of roles. The roles of a group will be granted
to all users belonging to it.

**Note:**

> Symfony2 supports role inheritance so inheriting roles from groups is not
> always needed. If the role inheritance is enough for your use case, it
> is better to use it instead of groups as it is more efficient (loading
> the groups triggers the database).

To use the groups, you need to explicitly enable this functionality in your
configuration. The only mandatory configuration is the fully qualified class
name (FQCN) of your `Group` class which must implement `FOS\UserBundle\Model\GroupInterface`.

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
    <fos_user:group group-class="Acme\UserBundle\Entity\Group" />
</fos_user:config>
```

### The Group class

The simplest way to create a Group class is to extend the mapped superclass
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
