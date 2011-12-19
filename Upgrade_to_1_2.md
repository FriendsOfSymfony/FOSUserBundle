Upgrading from 1.1 to 1.2
=========================

This file describes the needed changes when upgrading from 1.1 to 1.2

### Removed the user-level algorithm.

If you are experiencing the exception 
`No encoder has been configured for account "Acme\DemoBundle\Entity\User"` 
after upgrading, please consider the following.

The encoder now needs to be configured in the SecurityBundle configuration
as described in the official documentation. If you were using the default
value of the bundle, the config should look like this to reuse the same settings:

```yaml
#app/config/security.yml
security:
    encoders:
        "FOS\UserBundle\Model\UserInterface":
            algorithm: sha512
            encode_as_base64: false
            iterations: 1
```
