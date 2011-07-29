How to update your bundle ?
===========================

This document explains how to upgrade from one FOSUSerBundle version to
the next one. It only discusses changes that need to be done when using
the "public" API of the bundle. If you "hack" the core, you should probably
follow the timeline closely anyway.

* The methods relative to the groups have been removed from `FOS\UserBundle\Model\UserInterface`
  and a new `FOS\UserBundle\Model\GroupableInterface` has been added (implemented
  by `FOS\UserBundle\Model\User`).

* The User class of the bundle does not contain the timestampable fields anymore
  as they were not used by the bundle.

* The `fos:user:changePassword` command has been renamed to `fos:user:change-password`.

* The way to configure the forms has been refactored to give more flexibility:

    * The configuration of the type now accepts the name of the type. You can
      register your own type by creating a tagged service in the container:

        <tag name="form.type" alias="acme_custom_type" />

    * The configuration of the handler now accepts a service id.

* The form classes have been moved to subnamespaces to keep them organized.

* The ACL implementation using JMSSecurityExtraBundle which was broken
  since Symfony beta2 has been removed.

* The Twig block has been renamed from `content` to `fos_user_content`.
