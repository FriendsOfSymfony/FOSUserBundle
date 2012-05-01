About FOSUserBundle User Manager
================================

In order to be storage agnostic, all operations on the user instances are
handled by a user manager implementing `FOS\UserBundle\Model\UserManagerInterface`.
Using it ensures that your code will continue to work if you change the storage.
The controllers provided by the bundle use the configured user manager instead
of interacting directly with the storage layer.

If you configure the `db_driver` option to `orm`, this service is an instance
of `FOS\UserBundle\Entity\UserManager`.

If you configure the `db_driver` option to `mongodb`, this service is an
instance of `FOS\UserBundle\Document\UserManager`.

If you configure the `db_driver` option to `couchdb`, this service is an
instance of `FOS\UserBundle\CouchDocument\UserManager`.

If you configure the `db_driver` option to `propel`, this service is an instance
of `FOS\UserBundle\Propel\UserManager`.

## Accessing the User Manager service

The user manager is available in the container as the `fos_user.user_manager`
service.

``` php
$userManager = $container->get('fos_user.user_manager');
```

## Creating a new User

A new instance of your User class can be created by the user manager.

``` php
$user = $userManager->createUser();
```

`$user` is now an instance of your user class.

**Note:**

> This method will not work if your user class has some mandatory constructor
> arguments.

## Retrieving the users

The user manager has a few methods to find users based on the unique fields
(username, email and confirmation token) and a method to retrieve all existing
users.

- findUserByUsername($username)
- findUserByEmail($username)
- findUserByUsernameOrEmail($value)  (check if the value looks like an email to choose)
- findUserByConfirmationToken($token)
- findUsers()

## Updating a User object

To save a user object, you can use the `updateUser` method of the user manager.
This method will update the encoded password and the canonical fields and
then persist the changes.

``` php
<?php
//...
$user = $userManager->createUser();
$user->setUsername('John');
$user->setEmail('john.doe@example.com');

$userManager->updateUser($user);
```

**Note:**

> To make it easier, the bundle comes with a Doctrine listener handling the
> update of the password and the canonical fields for you behind the scenes.
> If you always save the user through the user manager, you may want to disable
> it to improve performance.

In YAML:

``` yaml
# app/config/config.yml
fos_user:
    # ...
    use_listener: false
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

**Warning:**

> The Propel implementation does not have such a listener so you have to
> call the method of the user manager to save the user.

**Note:**

> For the Doctrine implementations, the default behavior is to flush the
> unit of work when calling the `updateUser` method. You can disable the
> flush by passing a second argument set to `false`.
> This will then be equivalent to calling `updateCanonicalFields` and
> `updatePassword`.

An ORM example:

``` php
<?php
public function MainController extends Controller
{
    public function updateAction($id)
    {
        $user = // get a user from the datastore

        $user->setEmail($newEmail);

        $this->get('fos_user.user_manager')->updateUser($user, false);

        // make more modifications to the database

        $this->getDoctrine()->getEntityManager()->flush();
    }
}
```
## Extending the User Manager

You can extend the user manger to include custom commands you require. To do so simply create a new file in your document or entity

Setup a service for your manager and pass in the required arguments. Also pass the argument to your user entity class.
```

services:
    acme_user.user_manager:
        class: acme\UserBundle\Etity\AcmeUserManager
        arguments: ["@security.encoder_factory", "@fos_user.util.username_canonicalizer", "@fos_user.util.email_canonicalizer","@fos_user.document_manager",%fos_user.model.user.class%]
```

ORM Manager example
```
namespace BIM\UserBundle\Entity;

use FOS\UserBundle\Entity\UserManager;
use Symfony\Component\DependencyInjection\ContainerAware;
/*
* This class extends the user manager so that we can add in specific needs of the admin tool.
*
*/

class AcmeUserManager extends UserManager 
{
   function somethingNew(){}
}
```

ODM Manager example
```
namespace BIM\UserBundle\Document;

use FOS\UserBundle\Document\UserManager;
use Symfony\Component\DependencyInjection\ContainerAware;
/*
* This class extends the user manager so that we can add in specific needs of the admin tool.
*
*/

class AcmeUserManager extends UserManager 
{
   function somethingNew(){}
}
```

Finally make your new manager extension available
In YAML:

``` yaml
fos_user:
    # ...
    service:
        user_manager: acme_user.user_manager
```

Now you can access your manager by the following method and it will include your custom manager methods and the standard methods available in fosUserBundle
```
    $userManager = $this->container->get('acme_user.user_manager');
```

## Overriding the User Manager

You can replace the default implementation of the user manager by defining
a service implementing `FOS\UserBundle\Model\UserManagerInterface` and
setting its id in the configuration.

The id of the default implementation is `fos_user.user_manager.default`

In app/config/config.yml:

``` yaml
fos_user:
    # ...
    service:
        user_manager: custom_user_manager_id
```
Your custom implementation can extend `FOS\UserBundle\Model\UserManager`
to reuse the common logic.

## SecurityBundle integration

The built-in user managers also implement `Symfony\Component\Security\Core\UserProviderInterface`
so they can be used as provider for the Security component.
