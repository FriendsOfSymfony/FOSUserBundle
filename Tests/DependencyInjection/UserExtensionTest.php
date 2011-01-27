<?php

namespace FOS\UserBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use FOS\UserBundle\DependencyInjection\UserExtension;
use Symfony\Component\Yaml\Parser;

class UserExtensionTest extends \PHPUnit_Framework_TestCase
{
    protected $configuration;

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUserLoadThrowsExceptionUnlessDatabaseDriverSet()
    {
        $loader = new UserExtension('testkernel');
        $config = $this->getEmptyConfig();
        unset($config['db_driver']);
        $loader->configLoad(array($config), new ContainerBuilder());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUserLoadThrowsExceptionUnlessDatabaseDriverIsValid()
    {
        $loader = new UserExtension('testkernel');
        $config = $this->getEmptyConfig();
        $config['db_driver'] = 'foo';
        $loader->configLoad(array($config), new ContainerBuilder());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUserLoadThrowsExceptionUnlessUserModelClassSet()
    {
        $loader = new UserExtension('testkernel');
        $config = $this->getEmptyConfig();
        unset($config['class']['model']['user']);
        $loader->configLoad(array($config), new ContainerBuilder());
    }

    public function testUserLoadModelClassWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('Application\MyBundle\Document\User', 'fos_user.model.user.class');
    }

    public function testUserLoadModelClass()
    {
        $this->createFullConfiguration();

        $this->assertParameter('user', 'fos_user.model.user.class');
    }

    public function testUserLoadManagerClassWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertHasDefinition('fos_user.user_manager');
    }

    public function testUserLoadManagerClass()
    {
        $this->createFullConfiguration();

        $this->assertHasDefinition('fos_user.user_manager');
    }

    public function testUserLoadFormClassWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('FOS\UserBundle\Form\UserForm', 'fos_user.form.user.class');
        $this->assertParameter('FOS\UserBundle\Form\ChangePasswordForm', 'fos_user.form.change_password.class');
    }

    public function testUserLoadFormClass()
    {
        $this->createFullConfiguration();

        $this->assertParameter('user', 'fos_user.form.user.class');
        $this->assertParameter('change_password', 'fos_user.form.change_password.class');
    }

    public function testUserLoadFormNameWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('fos_user_user_form', 'fos_user.form.user.name');
        $this->assertParameter('fos_user_change_password_form', 'fos_user.form.change_password.name');
    }

    public function testUserLoadFormName()
    {
        $this->createFullConfiguration();

        $this->assertParameter('user', 'fos_user.form.user.name');
        $this->assertParameter('change_password', 'fos_user.form.change_password.name');
    }

    public function testUserLoadFormServiceWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertHasDefinition('fos_user.form.user');
        $this->assertHasDefinition('fos_user.form.change_password');
    }

    public function testUserLoadFormService()
    {
        $this->createFullConfiguration();

        $this->assertHasDefinition('fos_user.form.user');
        $this->assertHasDefinition('fos_user.form.change_password');
    }

    public function testUserLoadControllerClassWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('FOS\UserBundle\Controller\UserController', 'fos_user.controller.user.class');
        $this->assertParameter('FOS\UserBundle\Controller\SecurityController', 'fos_user.controller.security.class');
    }

    public function testUserLoadControllerClass()
    {
        $this->createFullConfiguration();

        $this->assertParameter('user', 'fos_user.controller.user.class');
        $this->assertParameter('security', 'fos_user.controller.security.class');
    }

    public function testUserLoadControllerServiceWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertHasDefinition('fos_user.controller.user');
        $this->assertHasDefinition('fos_user.controller.security');
    }

    public function testUserLoadControllerService()
    {
        $this->createFullConfiguration();

        $this->assertHasDefinition('fos_user.controller.user');
        $this->assertHasDefinition('fos_user.controller.security');
    }

    public function testUserLoadConfirmationEmailWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter(false, 'fos_user.email.confirmation.enabled');
        $this->assertParameter('webmaster@example.com', 'fos_user.email.from_email');
        $this->assertParameter('FOSUserBundle:User:confirmationEmail', 'fos_user.email.confirmation.template');
        $this->assertParameter('FOSUserBundle:User:resettingPasswordEmail', 'fos_user.email.resetting_password.template');
    }

    public function testUserLoadConfirmationEmail()
    {
        $this->createFullConfiguration();

        $this->assertParameter('enabled', 'fos_user.email.confirmation.enabled');
        $this->assertParameter('from_email', 'fos_user.email.from_email');
        $this->assertParameter('template', 'fos_user.email.confirmation.template');
        $this->assertParameter('template', 'fos_user.email.resetting_password.template');
    }

    public function testUserLoadTemplateConfigWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('twig', 'fos_user.template.engine');
        $this->assertParameter('TwigBundle::form.html.twig', 'fos_user.template.theme');
    }

    public function testUserLoadTemplateConfig()
    {
        $this->createFullConfiguration();

        $this->assertParameter('engine', 'fos_user.template.engine');
        $this->assertParameter('theme', 'fos_user.template.theme');
    }

    public function testUserLoadEncoderConfigWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('sha512', 'fos_user.encoder.algorithm');
        $this->assertParameter(false, 'fos_user.encoder.encode_as_base64');
        $this->assertParameter(1, 'fos_user.encoder.iterations');
    }

    public function testUserLoadEncoderConfig()
    {
        $this->createFullConfiguration();

        $this->assertParameter('algorithm', 'fos_user.encoder.algorithm');
        $this->assertParameter('encode_as_base64', 'fos_user.encoder.encode_as_base64');
        $this->assertParameter('iterations', 'fos_user.encoder.iterations');
    }

    public function testUserLoadUtilClassWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('FOS\UserBundle\Util\Canonicalizer', 'fos_user.util.email_canonicalizer.class');
        $this->assertParameter('FOS\UserBundle\Util\Canonicalizer', 'fos_user.util.username_canonicalizer.class');
    }

    public function testUserLoadUtilClass()
    {
        $this->createFullConfiguration();

        $this->assertParameter('email_canonicalizer', 'fos_user.util.email_canonicalizer.class');
        $this->assertParameter('username_canonicalizer', 'fos_user.util.username_canonicalizer.class');
    }

    /**
     * @return ContainerBuilder
     */
    protected function createEmptyConfiguration()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new UserExtension('testkernel');
        $config = $this->getEmptyConfig();
        $loader->configLoad(array($config), $this->configuration);
        $this->assertTrue($this->configuration instanceof ContainerBuilder);
    }

    /**
     * @return ContainerBuilder
     */
    protected function createFullConfiguration()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new UserExtension('testkernel');
        $config = $this->getFullConfig();
        $loader->configLoad(array($config), $this->configuration);
        $this->assertTrue($this->configuration instanceof ContainerBuilder);
    }

    /**
     * getEmptyConfig
     *
     * @return array
     */
    protected function getEmptyConfig()
    {
        $yaml = <<<EOF
db_driver: mongodb
class:
    model:
        user: Application\MyBundle\Document\User
    form:
        user:            ~
        change_password: ~
    controller:
        user:     ~
        security: ~
    util:
        email_canonicalizer:    ~
        username_canonicalizer: ~
encoder:
    algorithm:        ~
    encode_as_base64: ~
    iterations:       ~
form_name:
    user:            ~
    change_password: ~
email:
    from_email: ~
    confirmation:
        enabled:    ~
        template:   ~
    resetting_password:
        template:   ~
template:
    engine: ~
    theme:  ~
EOF;
        $parser = new Parser();
        return $parser->parse($yaml);
    }

    protected function getFullConfig()
    {
        $config = $this->getEmptyConfig();
        array_walk_recursive($config, function(&$item, $key) {
            if (!is_array($item)) {
                $item = $key;
            }
        });
        $config['db_driver'] = 'orm';

        return $config;
    }

    public function assertAlias($value, $key)
    {
        $this->assertEquals($value, $this->configuration->getAlias($key), sprintf('%s alias is correct', $key));
    }

    public function assertParameter($value, $key)
    {
        $this->assertEquals($value, $this->configuration->getParameter($key), sprintf('%s parameter is correct', $key));
    }

    public function assertHasDefinition($id)
    {
        $this->assertTrue(($this->configuration->hasDefinition($id) ?: $this->configuration->hasAlias($id)));
    }

    protected function tearDown()
    {
        unset($this->configuration);
    }

}
