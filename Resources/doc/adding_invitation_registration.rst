FOSUserBundle Invitation
========================

Require an invitation to create a new user is a pattern mostly used for
early stage of a project. User enter their invitation code in order to
register.

Invitation model
----------------

First we need to add the invitation entity. An invitation is represented
by a unique code/identifier generated in the constructor::

    namespace AppBundle\Entity;

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
    }

Next we map our ``Invitation`` entity to our ``User`` with a one-to-one association::

    namespace AppBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Symfony\Component\Validator\Constraints as Assert;

    /** @ORM\Entity */
    class User extends \FOS\UserBundle\Model\User
    {
        /** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue(strategy="AUTO") */
        protected $id;

        /**
         * @ORM\OneToOne(targetEntity="Invitation")
         * @ORM\JoinColumn(referencedColumnName="code")
         * @Assert\NotNull(message="Your invitation is wrong", groups={"Registration"})
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

Add invitation to RegistrationFormType
--------------------------------------

Override the default registration form with your own::

    namespace AppBundle\Form;

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;

    class RegistrationFormType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder->add('invitation', 'app_invitation_type');
        }

        public function getParent()
        {
            return 'fos_user_registration';
        }

        public function getName()
        {
            return 'app_user_registration';
        }
    }

Create the invitation field::

    namespace AppBundle\Form;

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolverInterface;
    use Doctrine\ORM\EntityRepository;
    use AppBundle\Form\DataTransformer\InvitationToCodeTransformer;

    class InvitationFormType extends AbstractType
    {
        private $invitationTransformer;

        public function __construct(InvitationToCodeTransformer $invitationTransformer)
        {
            $this->invitationTransformer = $invitationTransformer;
        }

        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder->addModelTransformer($this->invitationTransformer);
        }

        public function setDefaultOptions(OptionsResolverInterface $resolver)
        {
            $resolver->setDefaults(array(
                'class' => 'AppBundle\Entity\Invitation',
                'required' => true,
            ));
        }

        public function getParent()
        {
            return 'text';
        }

        public function getName()
        {
            return 'app_invitation_type';
        }
    }

Create the custom data transformer::

    namespace AppBundle\Form\DataTransformer;

    use AppBundle\Entity\Invitation;
    use Doctrine\ORM\EntityManager;
    use Symfony\Component\Form\DataTransformerInterface;
    use Symfony\Component\Form\Exception\UnexpectedTypeException;

    /**
     * Transforms an Invitation to an invitation code.
     */
    class InvitationToCodeTransformer implements DataTransformerInterface
    {
        private $entityManager;

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
                throw new UnexpectedTypeException($value, 'AppBundle\Entity\Invitation');
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

            $dql = <<<DQL
    SELECT i
    FROM AppBundle:Invitation i
    WHERE i.code = :code
    AND NOT EXISTS(SELECT 1 FROM AppBundle:User u WHERE u.invitation = i)
    DQL;

            return $this->entityManager
                ->createQuery($dql)
                ->setParameter('code', $code)
                ->setMaxResults(1)
                ->getOneOrNullResult();
        }
    }


Register your custom form type in the container:

.. configuration-block::

    .. code-block:: yaml

        # app/config/services.yml
        services:
            app.form.registration:
                class: AppBundle\Form\RegistrationFormType
                tags:
                    - { name: "form.type", alias: "app_user_registration" }

            app.form.invitation:
                class: AppBundle\Form\InvitationFormType
                arguments: ['@app.form.data_transformer.invitation']
                tags:
                    - { name: "form.type", alias: "app_invitation_type" }

            app.form.data_transformer.invitation:
                class: AppBundle\Form\DataTransformer\InvitationToCodeTransformer
                arguments: ['@doctrine.orm.entity_manager']
                public: false

    .. code-block:: xml

        <!-- app/config/services.xml -->

        <?xml version="1.0" ?>

        <container xmlns="http://symfony.com/schema/dic/services"
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">

            <services>

                <service id="app.form.registration" class="AppBundle\Form\RegistrationFormType">
                    <tag name="form.type" alias="app_user_registration" />
                </service>

                <service id="app.form.invitation" class="AppBundle\Form\InvitationFormType">
                    <argument type="service" id="app.form.data_transformer.invitation"/>
                    <tag name="form.type" alias="app_invitation_type" />
                </service>

                <service id="app.form.data_transformer.invitation"
                    class="AppBundle\Form\DataTransformer\InvitationToCodeTransformer"
                    public="false
                >
                    <argument type="service" id="doctrine.orm.entity_manager"/>
                </service>

            </services>
        </container>

Next overwrite the default ``RegistrationFormType`` with the one just created :

.. code-block:: yaml

    # app/config/config.yml

    fos_user:
        registration:
            form:
                type: app_user_registration

You are done, go to your registration form to see the result.
