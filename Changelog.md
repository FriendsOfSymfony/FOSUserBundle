Changelog
=========

### 2.0.0 (2013-XX-XX)

* Removed the deprecated UserManager and GroupManager classes for the different Doctrine implementations
* [BC break] Refactored the structure of controller to dispatch events instead of using form handlers
* Removed all form handlers
* [BC break] Changed Datetime properties of default User entity that were nullable to default to null when no value supplied
* [BC break] Updated schema.xml for Propel BaseUser class to allow nullable and typehint accordingly

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
