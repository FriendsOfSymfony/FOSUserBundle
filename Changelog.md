Changelog
=========

### 4.1.0 (2026-02-13)

* Convert XML config files to other formats to fix the deprecation of XML config files in Symfony
* Add PHP routing files alongside the XML ones. Loading the XML routing files triggers a deprecation in Symfony 7.4.
* Fix deprecation in the UserChecker
* Fix the HashingPasswordUpdater to avoid trigger deprecations for `UserInterface::eraseCredentials`
* Deprecate `\FOS\UserBundle\Model\User::eraseCredentials`
* Remove support for Symfony < 7.3
* Remove support for PHP < 8.2

### 4.0.0 (2024-07-04)

* [BC break] Removed the CouchDB ODM integration
* [BC break] Added return types in most methods
* [BC break] Marked classes as final when they were `@final`
* Removed support for symfony <6.4
* Removed support for PHP <8.1
* Remove the mailer implementation based on Swiftmailer
* Added support for Symfony 7

### 3.4.0 (2024-06-25)

* Deprecated the TwigSwiftMailer implementation

### 3.3.0 (2024-06-24)

* Added a mailer implementation based on symfony/mailer and Twig
* Added tentative return types in most methods
* Deprecated the CouchDB ODM integration as the ODM is unmaintained

### 3.2.1 (2023-07-06)

* Fixed remaining deprecations with Symfony 6.3

### 3.2.0 (2023-07-06)

* Fixed deprecations with Symfony 6.3
* Fixed deprecations with Doctrine ORM (requires using DoctrineBundle 2.10.1 or newer for the fix to be effective)
* Fixed the way to access the session when enabling confirmation emails
* Fixed the way to access the firewall name when enabling the registration feature

### 3.1.0 (2022-10-26)

* Added support for Symfony 6

### 3.0.2 (2022-10-26)

* Fixed support for the remember-me in the programmatic login when using the new authentication system of Symfony.
* Fixed some deprecations when using Symfony 5.4.

### 3.0.1 (2022-08-27)

* Fixed the wiring of controllers to avoid a deprecation warning when using Twig.

### 3.0.0 (2022-04-28)

* [BC break] Change the base class of controllers to use the `AbstractController` (but extending the controllers is not officially supported anymore).
* [BC break] Remove the group feature
* [BC break] Change the base class for events to `Symfony\Contracts\EventDispatcher\Event` instead of `Symfony\Component\EventDispatcher\Event`
* [BC break] Remove the `Symfony\Component\Security\Core\User\AdvancedUserInterface` methods from our `UserInterface`
* [BC break] The ResettingListener now longer blocks password resetting requests based on the `isAccountNonLocked` method of the `AdvancedUserInterface`. Projects customizing `isAccountNonLocked` for that purpose should instead register their own listener for the `FOSUserEvents::RESETTING_RESET_REQUEST` event to set a response instead of processing the request.
* [BC break] Made `\FOS\UserBundle\Model\User::serialize` and `\FOS\UserBundle\Model\User::unserialize` final. Child classes needing to extend the serialization must override `__serialize` and `__unserialize` instead.
* [BC break] `\FOS\UserBundle\Event\GetResponseNullableUserEvent` no longer inherits from `\FOS\UserBundle\Event\GetResponseUserEvent` and `\FOS\UserBundle\Event\UserEvent` as that was breaking variance rules.
* [BC break] A few methods of `FOS\UserBundle\Model\User` now have return types (in methods where Symfony 6 requires them)
* [BC break] The legacy mailer based on SwiftMailer and symfony/templating is no longer used by default. Selecting a mailer service is now mandatory when using a feature needing the mailer.
* [BC break] Remove the legacy mailer based on SwiftMailer and symfony/templating. Use `fos_user.mailer.twig_swift` or a custom mailer service.
* Add support for Symfony 5.
* Add return types in most methods.
* Add autowiring support for `FOS\UserBundle\Mailer\MailerInterface`

### 2.2.4 (2022-01-14)

* Fixed a deprecation warning reported by DebugClassLoader in the AdvancedUserInterface BC layer due to the change done in 2.2.3.

### 2.2.3 (2022-01-14)

* Added missing deprecations on some group-related event classes
* Fixed an invalid report of `UserInterface` being deprecated in static analyzers
* Fixed the documented return type for `\FOS\UserBundle\Event\GetResponseNullableUserEvent::getUser`

### 2.2.2 (2021-09-08)

* Fixed a deprecation warning about groups being triggered when loading all Doctrine metadata.

### 2.2.1 (2021-09-08)

* Fixed a deprecation warning about groups being triggered when loading the User class of the bundle.

### 2.2.0 (2021-08-26)

* Deprecated the Groups feature.
* Marked all controllers final.
* Marked internal classes as such.
* Added Mongolian translation.
* Added an email provider.
* Added a custom user checker.
* Added PHP 7.4 and PHP 8.0 support.
* Removed fieldName attribute in MongoDB mapping.
* Registration confirmation now redirects to login page if token is invalid.
* User model will not rely on `AdvancedUserInterface` anymore.
* Self-salting password encoders will not create a salt anymore.
* FlashListener constructor now accepts `SessionInterface`.
* Fixed several Symfony deprecation notices.
* Fixed several translations.
* Bumped the min PHP version to 7.1.3.
* Bumped the min Symfony version to 4.4.
* Added compatibility with Twig 3.
* Added compatibility with doctrine/persistence 2.

### 2.1.2 (2018-03-08)

* Fixed compatibility of controllers with Symfony 2.8

### 2.1.1 (2018-02-20)

* Fixed the check for the required session, to account for the fact it is not always required.

### 2.1.0 (2018-02-16)

* Dropped Symfony < 2.8 support.
* Add Symfony 4 compatibility.
* Refactored controllers and commands to use DI. Projects extending these classes will need to adapt their code (but should rather use supported extension points when possible).
* Redirect to login when requesting resetting password with invalid token.
* Added autocomplete hints for password inputs.
* Fixed several incorrect Turkish translations.

### 2.0.2 (2017-11-29)

* Fix empty password in ChangePasswordFormType.
* Fix empty password in ProfileFormType.
* Introduced aliases for autowiring user and group managers.
* Added Bengali translation.
* Added Galician translation.
* Updated Danish translation.
* Updated Japanese translation.

### 2.0.1 (2017-05-31)

* Add SwiftMailer 6 compatibility.
* Inject firewall `user_checker` into `LoginManager`.
* Updated English translation.
* Updated Estonian translation.
* Updated Persian translation.
* Updated Turkish translation.
* Updated several docs.

### 2.0.0 (2017-03-29)

* Removed default `fos_user.from_email` configuration values.
* Removed usage of internal Twig APIs when rendering emails.
* Add a timeout for the reset retry request.
* Add Esperanto translations.
* Fixed incorrect confirmation url.
* Commented outdated entries in several translation files.
* [BC break] Use `UserManager::getRepository()` instead of `UserManager::$repository`.
* [BC break] Use `UserManager::getClass()` instead of `UserManager::$class`.

### 2.0.0-beta2 (2017-01-31)

* Use ceil in `ResettingController` for a better token lifetime approximation.
* Removed unused translation keys.
* Removed form deprecations.
* Use `@`-based Twig syntax for templates.
* Improved several language files.
* Improved documentation.
* Ability to disable the authentication listener.
* Removed `DateUtil` class.
* [BC break] Changed validation max length to match the database structure.

### 2.0.0-beta1 (2016-11-29)

* Dropped Symfony < 2.7 support.
* Dropped PHP < 5.5 support.
* Exclude tests from autoloader.
* Allow to use POST for logout.
* Fix UserPassword constraint validation groups.
* Harmonized email detection in `UserManager`.
* Added unique index for `confirmation_token` field.
* Added Kyrgyz translation files.
* Added user manipulator events.
* Replaced `checkPostAuth` by `checkPreAuth` in `AuthenticationListener`.
* [BC break] Method `ResettingController::getObfuscatedEmail` has been removed.
* [BC break] Renamed templates to underscore case.
* [BC break] Removed `UserManager::refreshUser`.
* [BC break] Removed `UserManager::loadUserByUsername`.
* [BC break] Removed `UserManager::supportsClass`.
* [BC break] Removed `FOS\UserBundle\Model\User` properties `$locked`, `$expired`, `$expiredAt`, `$credentialsExpired`, `$credentialsExpiredAt` and associated setter and getter ([see here](https://github.com/FriendsOfSymfony/FOSUserBundle/blob/master/Upgrade.md#200-alpha3-to-200-beta1)).
* [BC break] The signature of the `Initializer` constructor has changed.
* [BC break] The signature of the `LoginManager` constructor has changed.
* [BC break] The signature of the `UserListener` constructor has changed.
* [BC break] The signature of the `UserManager` constructor has changed.
* [BC break] The translation key `resetting.request.invalid_username` has been removed.
* [BC break] The propel dependency was dropped.
* [BC break] The `salt` field of the `User` class is now nullable.

### 2.0.0-alpha3 (2015-09-15)

* Reverted the removed of the `expired` and `credentialsExpired` properties as the BC break could lead to corrupted objects being created if server sessions are not cleared when upgrading the bundle.

### 2.0.0-alpha2 (2015-09-15)

* The minimum requirement for Doctrine is now ORM 2.4 and MongoDB ODM 1.0-alpha10.
* [BC break] The deprecated entity classes have been removed.
* The minimum requirement for Symfony has been bumped to 2.3 (older versions are already EOLed).
* [BC break] ``UserInterface::isUser`` has been removed as it was used only by the old validation logic removed a long time ago.
* [BC break] The ``FOSUserBundle:Security:login.html.twig`` template now receives an AuthenticationException in the ``error``
  variable rather than an error message.
* [BC break] The templating engine configuration has been removed, as well as the related code.
* [BC break] Changed the XML namespace to `http://friendsofsymfony.github.io/schema/dic/user`
* [BC break] Added `UserInterface::getId`.
* [BC break][Reverted] Removed unused properties `expired` and `credentialsExpired` including corresponding methods. This may break code,
   which makes use of this methods, extending classes, and/or existing installations because of missing mappings for required db fields.

### 2.0.0-alpha1 (2014-09-26)

* Updated many translations.
* Changed the way to pass the email to the page asking to check the email to avoid issues with non-blocking sessions.
* Changed the fos_user_security_check route to enforce POST.
* Removed the deprecated UserManager and GroupManager classes for the different Doctrine implementations.
* [BC break] Refactored the structure of controller to dispatch events instead of using form handlers.
* Removed all form handlers.
* [BC break] Changed Datetime properties of default User entity that were nullable to default to null when no value supplied.
* [BC break] Updated schema.xml for Propel BaseUser class to allow nullable and typehint accordingly.

### 1.3.8 (xxxx-xx-xx)

* Fixed invalid `isAccountNonExpired` timestamp when year is 2038
* Fixed validation of blank passwords
* Removed any new lines in email subjects
* Added trailing dot flash messages
* Added trailing dot validator messages
* Added Galician translation
* Use `random_bytes` to generate tokens

### 1.3.7 (2016-11-22)

* Fixed some yaml errors in translation files
* Fixed bad credentials translations
* Fixed canonicalizer with illegal chars
* Fixed deprecated routing configuration
* Fixed class name check in `UserProvider::refreshUser()`
* Updated several translation files
* Removed colons from translation files
* Updated several documentation examples
* Converted documentation to rst format

### 1.3.6 (2015-06-01)

* Fix compatibility with Symfony 2.7 #1777

### 1.3.5 (2014-09-04)

This release fixes a security issue. You are encouraged to update
as soon as possible.

BC break: The characters used in generated tokens have changed. They
now include dashes and underscores as well. Any routing requirement
matching them should be updated to ``[\w\-]+``.

* Fixed the TokenGenerator to preserve entropy.

### 1.3.4 (2014-06-13)

* Fixed the compatibility with FrameworkBundle 2.5
* Fixed a few issues in translations
* Enforce the POST method for the login_check route

### 1.3.3 (2013-09-23)

This releases prevents a potential DOS attack. You are encouraged to update
as soon as possible.

* Added a max length validation on the password

### 1.3.2 (2013-05-25)

* Changed the flash message handling to use the non-deprecated api
* Updated the composer constraint to allow Symfony 2.3

### 1.3.1 (2012-12-22)

* Replaced the deprecated validation constraints by the new ones
* Added an error message when the repeated password is invalid
* Updated many translations
* Made the composer requirement compatible with Symfony 2.2.*
* Fixed the handling of the target url after the registration

### 1.3.0 (2012-10-06)

* Refactored the Propel implementation to get rid of the UserProxy
* Changed the expectation for `FOS\UserBundle\Model\GroupableInterface#getGroups` to `Traversable`
* Moved the role constants to the UserInterface instead of the abstract User class
* Refactored the Doctrine implementations to use the same manager classes
* Removed the custom uniqueness validation in favor of the core constraints
* Added getRedirectionUrl method to ProfileController
* Added an extension point in the registration handler
* Moved the generation of the token to a dedicated class
* Added new user provider classes. They should be preferred over using the UserManager as UserProvider.
* Removed the custom password validation in favor of the Symfony 2.1 constraint
* Refactored the translation of form labels using the translation_domain option of Symfony 2.1
* Bumped the requirement to Symfony 2.1

### 1.2.5 (2013-09-23)

This releases prevents a potential DOS attack. You are encouraged to update
as soon as possible.

* Added a max length on the password field
* Fixed a Yaml parsing error in the Japanese translations

### 1.2.4 (2012-07-10)

This release fixes another security issue. Please update to it as soon as possible.

* Fixes a security issue where the session could be hijacked

### 1.2.3 (2012-07-10)

* Fixed the serialization of users to include the id

### 1.2.2 (2012-07-10)

* Fixed a bug in the previous fix

### 1.2.1 (2012-07-10)

This release fixes a security issue. You are encouraged to update to it as soon
as possible.

* Fixed the user refreshing to check the identity by primary key instead of username

### 1.2.0 (2012-04-12)

* Prefixed fos table names in propel schema with "fos_" to avoid using reserved sql words
* Added a fluent interface for the entities
* Added a mailer able to use twig blocks for the each part of the message
* Fixed the authentication in case of locked or disabled users. Github issue #464
* Add CSRF protection to the login form
* Added translations: bg, hr
* Updated translations
* Added translations for the validation errors and the login error
* Removed the user-level algorithm. Use FOSAdvancedEncoderBundle instead if you need such feature.
* Fixed resetting password clearing the token but not the token expiration. Github issue #501
* Renamed UsernameToUsernameTransformer to UserToUsernameTransformer and changed its service ID to `fos_user.user_to_username_transformer`.

### 1.1.0  (2011-12-15)

* Added "custom" as valid driver
* Hide part of the email when requesting a password reset
* Changed the validation messages to translation keys
* Added the default validation group by default
* Fixed updating of changed fields in listener. Github issue #403
* Added support for Propel
* Added composer.json
* Made it possible to override the role constants in derived User class
* Updated translations: da, de, en, es, et, fr, hu, lb, nl, pl, pt_BR, pt_PT, ru
* Added translations: ca, cs, it, ja, ro, sk, sl, sv
* Changed the instanceof check for refreshUser to class instead of interface to allow multiple firewalls and correct use of UnsupportedUserException
* Added an extension point in the form handlers. Closes #291
* Rewrote the documentation entirely

### 1.0.0  (2011-08-01)

* Initial release
