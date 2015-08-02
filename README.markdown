FOSUserBundle
=============

The FOSUserBundle adds support for a database-backed user system in Symfony2.
It provides a flexible framework for user management that aims to handle
common tasks such as user registration and password retrieval.

Features include:

- Users can be stored via Doctrine ORM, MongoDB/CouchDB ODM or Propel
- Registration support, with an optional confirmation per email
- Password reset support
- Unit tested

**Note:** This bundle does *not* provide an authentication system but can
provide the user provider for the core [SecurityBundle](https://symfony.com/doc/current/book/security.html).

**Caution:** This bundle is developed in sync with [symfony's repository](https://github.com/symfony/symfony).
For Symfony 2.0.x, you need to use the 1.2.0 release of the bundle (or lower)

[![Build Status](https://travis-ci.org/FriendsOfSymfony/FOSUserBundle.svg?branch=1.3.x)](https://travis-ci.org/FriendsOfSymfony/FOSUserBundle)

Documentation
-------------

The documentation of this bundle is hosted on symfony.com:

[Read the Documentation for this version](http://symfony.com/doc/1.3.x/bundles/FOSUserBundle/index.html)

Installation
------------

All the installation instructions are located in [documentation](Resources/doc/index.rst).

License
-------

This bundle is under the MIT license. See the complete license in the bundle:

    Resources/meta/LICENSE

About
-----

UserBundle is a [knplabs](https://github.com/knplabs) initiative.
See also the list of [contributors](https://github.com/FriendsOfSymfony/FOSUserBundle/contributors).

Reporting an issue or a feature request
---------------------------------------

Issues and feature requests are tracked in the [Github issue tracker](https://github.com/FriendsOfSymfony/FOSUserBundle/issues).

When reporting a bug, it may be a good idea to reproduce it in a basic project
built using the [Symfony Standard Edition](https://github.com/symfony/symfony-standard)
to allow developers of the bundle to reproduce the issue by simply cloning it
and following some steps.
