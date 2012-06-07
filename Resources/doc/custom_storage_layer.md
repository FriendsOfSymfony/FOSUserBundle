Using a custom storage layer
============================

FOSUserBundle has been designed to allow you to easily change the storage
layer used by your application and keep and keep all of the functionality
provided by the bundle.

Implementing a new storage layer requires providing two classes: the user
implementation and the corresponding user manager (you will of course need
two other classes if you want to use the groups).

The user implementation must implement `FOS\UserBundle\Model\UserInterface`
and the user manager must implement `FOS\UserBundle\Model\UserManagerInterface`.
The `FOS\UserBundle\Model` namespace provides base classes to make it easier to
implement these interfaces.

**Note:**

> You need to take care to always call `updateCanonicalFields` and `updatePassword`
> before saving a user. This is done when calling `updateUser` so you will
> be safe if you always use the user manager to save the users.
> If your storage layer gives you a hook in its saving process, you can use
> it to make your implementation more flexible (this is done for Doctrine
> using listeners for instance)

## Configuring FOSUserBundle to use your implementation

To use your own implementation, create a service for your user manager. The
following example will assume that its id is `acme_user.custom_manager`.

``` yaml
#app/config/config.yml
fos_user:
    db_driver: custom  # custom means that none of the built-in implementation is used
    user_class: Acme\UserBundle\Model\CustomUser
    service:
        user_manager: acme_user.custom_manager
    firewall_name: main
```

**Note:**

> Your own service can be a private one. FOSUserBundle will create an alias
> to make it available through `fos_user.user_manager`.
