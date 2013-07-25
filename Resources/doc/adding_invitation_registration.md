FOSUserBundle Invitation
========================

Require an invitation to create a new user is a pattern mostly used for
early stage of a project. User enter their invitation code in order to
register.

### Invitation model

First we need to add the invitation entity. An invitation is represented
by a unique code/identifier generated in the constructor:

```php
<?php

namespace Acme\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity */
class Invitation
{
    /** @ORM\Id @ORM\Column(type="string", length=6) */
    protected $code;

    /** @ORM\Column(type="string", length=256) */
    protected $email;

    /**
     * When sending invitation be sure to set this value to `true`
     *
     * It can prevent invitations from being sent twice
     *
     * @ORM\Column(type="boolean")
     */
    protected $sent = false;

    /** @ORM\OneToOne(targetEntity="User", mappedBy="invitation", cascade={"persist", "merge"}) */
    protected $user;

    public function __construct()
    {
        // generate identifier only once, here a 6 characters length code
        $this->code = substr(md5(uniqid(rand(), true)), 0, 6);
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function isSent()
    {
        return $this->sent;
    }

    public function send()
    {
        $this->sent = true;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }
}
```

Next we map our `Invitation` entity to our `User` with a one-to-one

```php
<?php

namespace Acme\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/** @ORM\Entity */
class User extends \FOS\UserBundle\Entity\User
{
    /** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue(strategy="AUTO") */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Invitation", inversedBy="user")
     * @ORM\JoinColumn(referencedColumnName="code")
     * @Assert\NotNull(message="Your invitation is wrong")
     */
    protected $invitation;

    public function setInvitation(Invitation $invitation)
    {
        $this->invitation = $invitation;
    }

    public function getInvitation()
    {
        return $this->invitation;
    }
}
```

### Add invitation to RegistrationFormType

Override the default registration form with your own:

```php
<?php

namespace Acme\UserBundle\Form\Type;

use FOS\UserBundle\Form\Type\RegistrationFormType as BaseRegistrationFormType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationFormType extends BaseRegistrationFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('invitation', 'acme_invitation_type');
    }

    public function getName()
    {
        return 'acme_user_registration';
    }
}
```

Create the invitation field:

```php
<?php

namespace Acme\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Acme\UserBundle\Form\DataTransformer\InvitationToCodeTransformer;

class InvitationFormType extends AbstractType
{
    protected $invitationTransformer;

    public function __construct(InvitationToCodeTransformer $invitationTransformer)
    {
        $this->invitationTransformer = $invitationTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer($this->invitationTransformer, true);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'class' => 'Acme\UserBundle\Entity\Invitation',
            'required' => true,
        ));
    }

    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'acme_invitation_type';
    }
}
```

Create the custom data transformer:

```php
<?php

namespace Acme\UserBundle\Form\DataTransformer;

use Acme\UserBundle\Entity\Invitation;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

/**
 * Transforms an Invitation to an invitation code.
 */
class InvitationToCodeTransformer implements DataTransformerInterface
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof Invitation) {
            throw new UnexpectedTypeException($value, 'Acme\UserBundle\Entity\Invitation');
        }

        return $value->getCode();
    }

    public function reverseTransform($value)
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        return $this->entityManager
            ->getRepository('Acme\UserBundle\Entity\Invitation')
            ->findOneBy(array(
                'code' => $value,
                'user' => null,
            ));
    }
}
```


Register your custom form type in the container:

```xml
<!-- src/Acme/UserBundle/Resources/config/services.xml -->

<?xml version="1.0" ?>

<container xmlns="http://symfony.com/schema/dic/services"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">

    <services>

        <service id="acme.registration.form.type" class="Acme\UserBundle\Form\Type\RegistrationFormType">
            <argument>%fos_user.model.user.class%</argument>
            <tag name="form.type" alias="acme_user_registration" />
        </service>

        <service id="acme.invitation.form.type" class="Acme\UserBundle\Form\Type\InvitationFormType">
            <argument type="service" id="acme.invitation.form.data_transformer"/>
            <tag name="form.type" alias="acme_invitation_type" />
        </service>

        <service id="acme.invitation.form.data_transformer" class="Acme\UserBundle\Form\DataTransformer\InvitationToCodeTransformer">
            <argument type="service" id="doctrine.orm.entity_manager"/>
        </service>


    </services>
</container>
```

Or if you prefer the Yaml version:

```yaml

services:

    acme.registration.form.type:
        class: Acme\UserBundle\Form\Type\RegistrationFormType
        arguments: [%fos_user.model.user.class%]
        tags: [{ name: "form.type", alias: "acme_user_registration" }]

    acme.invitation.form.type:
        class: Acme\UserBundle\Form\Type\InvitationFormType
        arguments: [@acme.invitation.form.data_transformer]
        tags: [{ name: "form.type", alias: "acme_invitation_type" }]

    acme.invitation.form.data_transformer:
        class: Acme\UserBundle\Form\DataTransformer\InvitationToCodeTransformer
        arguments: [@doctrine.orm.entity_manager]

```

Next overwrite the default `RegistrationFormType` with the one just created :


```yaml
# config.yml

fos_user:
    registration:
        form:
            type: acme_user_registration
```

Your done, go to your registration form to see the result.
