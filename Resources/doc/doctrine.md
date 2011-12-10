More about Doctrine implementations
===================================

FOSUserBundle was first written for Doctrine-based storage layers. This chapter
describes some things specific to these implementations.

### Using a different object manager than the default one

Using the default configuration , FOSUserBundle will use the default doctrine
object manager. If you are using multiple ones and want to handle your users
with a non-default one, you can change the object manager used in the configuration
by giving its name to FOSUserBundle.

```yaml
# app/config/config.yml
fos_user:
    db_driver: orm
    model_manager_name: non_default # the name of your entity manager
```

**Note:**

> Using the default object manager is done by setting the configuration option
> to `null` which is the default value.

### Replacing the mapping of the bundle

None of the Doctrine projects currently allow overwriting part of the mapping
of a mapped superclass in the child entity.

If you need to change the mapping (for instance to adapt the field names
to a legacy database), the only solution is to write the whole mapping again
without inheriting the mapping from the mapped superclass. In such case,
your entity should extend directly from `FOS\UserBundle\Model\User` (and
`FOS\UserBundle\Model\Group` for the group).

**Warning:**

> It is highly recommended to map all fields used by the bundle (see the
> mapping files of the bundle in `Resources/config/doctrine/`). Omitting
> them can lead to unexpected behaviors and should be done carefully.
