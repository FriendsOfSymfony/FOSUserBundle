<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use FOS\UserBundle\DependencyInjection\FOSUserExtension;
use Symfony\Component\Yaml\Parser;

class FOSUserExtensionTest extends \PHPUnit_Framework_TestCase
{
    /** @var ContainerBuilder */
    protected $configuration;

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testUserLoadThrowsExceptionUnlessDatabaseDriverSet()
    {
        $loader = new FOSUserExtension();
        $config = $this->getEmptyConfig();
        unset($config['db_driver']);
        $loader->load(array($config), new ContainerBuilder());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testUserLoadThrowsExceptionUnlessDatabaseDriverIsValid()
    {
        $loader = new FOSUserExtension();
        $config = $this->getEmptyConfig();
        $config['db_driver'] = 'foo';
        $loader->load(array($config), new ContainerBuilder());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testUserLoadThrowsExceptionUnlessFirewallNameSet()
    {
        $loader = new FOSUserExtension();
        $config = $this->getEmptyConfig();
        unset($config['firewall_name']);
        $loader->load(array($config), new ContainerBuilder());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testUserLoadThrowsExceptionUnlessGroupModelClassSet()
    {
        $loader = new FOSUserExtension();
        $config = $this->getFullConfig();
        unset($config['group']['group_class']);
        $loader->load(array($config), new ContainerBuilder());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testUserLoadThrowsExceptionUnlessUserModelClassSet()
    {
        $loader = new FOSUserExtension();
        $config = $this->getEmptyConfig();
        unset($config['user_class']);
        $loader->load(array($config), new ContainerBuilder());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testCustomDriverWithoutManager()
    {
        $loader = new FOSUserExtension();
        $config = $this->getEmptyConfig();
        $config['db_driver'] = 'custom';
        $loader->load(array($config), new ContainerBuilder());
    }

    public function testCustomDriver()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new FOSUserExtension();
        $config = $this->getEmptyConfig();
        $config['db_driver'] = 'custom';
        $config['service']['user_manager'] = 'acme.user_manager';
        $loader->load(array($config), $this->configuration);

        $this->assertNotHasDefinition('fos_user.user_manager.default');
        $this->assertAlias('acme.user_manager', 'fos_user.user_manager');
        $this->assertParameter('custom', 'fos_user.storage');
    }

    public function testDisableRegistration()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new FOSUserExtension();
        $config = $this->getEmptyConfig();
        $config['registration'] = false;
        $loader->load(array($config), $this->configuration);
        $this->assertNotHasDefinition('fos_user.registration.form.factory');
    }

    public function testDisableResetting()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new FOSUserExtension();
        $config = $this->getEmptyConfig();
        $config['resetting'] = false;
        $loader->load(array($config), $this->configuration);
        $this->assertNotHasDefinition('fos_user.resetting.form.factory');
    }

    public function testDisableProfile()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new FOSUserExtension();
        $config = $this->getEmptyConfig();
        $config['profile'] = false;
        $loader->load(array($config), $this->configuration);
        $this->assertNotHasDefinition('fos_user.profile.form.factory');
    }

    public function testDisableChangePassword()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new FOSUserExtension();
        $config = $this->getEmptyConfig();
        $config['change_password'] = false;
        $loader->load(array($config), $this->configuration);
        $this->assertNotHasDefinition('fos_user.change_password.form.factory');
    }

    public function testUserLoadModelClassWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('Acme\MyBundle\Document\User', 'fos_user.model.user.class');
    }

    public function testUserLoadModelClass()
    {
        $this->createFullConfiguration();

        $this->assertParameter('Acme\MyBundle\Entity\User', 'fos_user.model.user.class');
    }

    public function testUserLoadManagerClassWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('mongodb', 'fos_user.storage');
        $this->assertParameter(null, 'fos_user.model_manager_name');
        $this->assertAlias('fos_user.user_manager.default', 'fos_user.user_manager');
        $this->assertNotHasDefinition('fos_user.group_manager');
    }

    public function testUserLoadManagerClass()
    {
        $this->createFullConfiguration();

        $this->assertParameter('orm', 'fos_user.storage');
        $this->assertParameter('custom', 'fos_user.model_manager_name');
        $this->assertAlias('acme_my.user_manager', 'fos_user.user_manager');
        $this->assertAlias('fos_user.group_manager.default', 'fos_user.group_manager');
    }

    public function testUserLoadFormClassWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('fos_user_profile', 'fos_user.profile.form.type');
        $this->assertParameter('fos_user_registration', 'fos_user.registration.form.type');
        $this->assertParameter('fos_user_change_password', 'fos_user.change_password.form.type');
        $this->assertParameter('fos_user_resetting', 'fos_user.resetting.form.type');
    }

    public function testUserLoadFormClass()
    {
        $this->createFullConfiguration();

        $this->assertParameter('acme_my_profile', 'fos_user.profile.form.type');
        $this->assertParameter('acme_my_registration', 'fos_user.registration.form.type');
        $this->assertParameter('acme_my_group', 'fos_user.group.form.type');
        $this->assertParameter('acme_my_change_password', 'fos_user.change_password.form.type');
        $this->assertParameter('acme_my_resetting', 'fos_user.resetting.form.type');
    }

    public function testUserLoadFormNameWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('fos_user_profile_form', 'fos_user.profile.form.name');
        $this->assertParameter('fos_user_registration_form', 'fos_user.registration.form.name');
        $this->assertParameter('fos_user_change_password_form', 'fos_user.change_password.form.name');
        $this->assertParameter('fos_user_resetting_form', 'fos_user.resetting.form.name');
    }

    public function testUserLoadFormName()
    {
        $this->createFullConfiguration();

        $this->assertParameter('acme_profile_form', 'fos_user.profile.form.name');
        $this->assertParameter('acme_registration_form', 'fos_user.registration.form.name');
        $this->assertParameter('acme_group_form', 'fos_user.group.form.name');
        $this->assertParameter('acme_change_password_form', 'fos_user.change_password.form.name');
        $this->assertParameter('acme_resetting_form', 'fos_user.resetting.form.name');
    }

    public function testUserLoadFormServiceWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertHasDefinition('fos_user.profile.form.factory');
        $this->assertHasDefinition('fos_user.registration.form.factory');
        $this->assertNotHasDefinition('fos_user.group.form.factory');
        $this->assertHasDefinition('fos_user.change_password.form.factory');
        $this->assertHasDefinition('fos_user.resetting.form.factory');
    }

    public function testUserLoadFormService()
    {
        $this->createFullConfiguration();

        $this->assertHasDefinition('fos_user.profile.form.factory');
        $this->assertHasDefinition('fos_user.registration.form.factory');
        $this->assertHasDefinition('fos_user.group.form.factory');
        $this->assertHasDefinition('fos_user.change_password.form.factory');
        $this->assertHasDefinition('fos_user.resetting.form.factory');
    }

    public function testUserLoadConfirmationEmailWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter(false, 'fos_user.registration.confirmation.enabled');
        $this->assertParameter(array('webmaster@example.com' => 'webmaster'), 'fos_user.registration.confirmation.from_email');
        $this->assertParameter('FOSUserBundle:Registration:email.txt.twig', 'fos_user.registration.confirmation.template');
        $this->assertParameter('FOSUserBundle:Resetting:email.txt.twig', 'fos_user.resetting.email.template');
        $this->assertParameter(array('webmaster@example.com' => 'webmaster'), 'fos_user.resetting.email.from_email');
        $this->assertParameter(86400, 'fos_user.resetting.token_ttl');
    }

    public function testUserLoadConfirmationEmail()
    {
        $this->createFullConfiguration();

        $this->assertParameter(true, 'fos_user.registration.confirmation.enabled');
        $this->assertParameter(array('register@acme.org' => 'Acme Corp'), 'fos_user.registration.confirmation.from_email');
        $this->assertParameter('AcmeMyBundle:Registration:mail.txt.twig', 'fos_user.registration.confirmation.template');
        $this->assertParameter('AcmeMyBundle:Resetting:mail.txt.twig', 'fos_user.resetting.email.template');
        $this->assertParameter(array('reset@acme.org' => 'Acme Corp'), 'fos_user.resetting.email.from_email');
        $this->assertParameter(1800, 'fos_user.resetting.token_ttl');
    }

    public function testUserLoadUtilServiceWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertAlias('fos_user.mailer.default', 'fos_user.mailer');
        $this->assertAlias('fos_user.util.canonicalizer.default', 'fos_user.util.email_canonicalizer');
        $this->assertAlias('fos_user.util.canonicalizer.default', 'fos_user.util.username_canonicalizer');
    }

    public function testUserLoadUtilService()
    {
        $this->createFullConfiguration();

        $this->assertAlias('acme_my.mailer', 'fos_user.mailer');
        $this->assertAlias('acme_my.email_canonicalizer', 'fos_user.util.email_canonicalizer');
        $this->assertAlias('acme_my.username_canonicalizer', 'fos_user.util.username_canonicalizer');
    }

    public function testUserLoadFlashesByDefault()
    {
        $this->createEmptyConfiguration();

        $this->assertHasDefinition('fos_user.listener.flash');
    }

    public function testUserLoadFlashesCanBeDisabled()
    {
        $this->createFullConfiguration();

        $this->assertNotHasDefinition('fos_user.listener.flash');
    }

    /**
     * @dataProvider userManagerSetFactoryProvider
     */
    public function testUserManagerSetFactory($dbDriver, $managerService, $doctrineService)
    {
        $this->configuration = new ContainerBuilder();
        $loader = new FOSUserExtension();
        $config = $this->getEmptyConfig();
        $config['db_driver'] = $dbDriver;
        $loader->load(array($config), $this->configuration);

        $definition = $this->configuration->getDefinition($managerService);

        if (method_exists($definition, 'getFactory')) {
            $factory = array(new Reference($doctrineService), 'getManager');
            $this->assertEquals($factory, $definition->getFactory());
        } else {
            $this->assertEquals($doctrineService, $definition->getFactoryService());
            $this->assertEquals('getManager', $definition->getFactoryMethod());
        }
    }

    public function userManagerSetFactoryProvider()
    {
        return array(
            array('orm', 'fos_user.entity_manager', 'doctrine'),
            array('couchdb', 'fos_user.document_manager', 'doctrine_couchdb'),
            array('mongodb', 'fos_user.document_manager', 'doctrine_mongodb'),
        );
    }

    protected function createEmptyConfiguration()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new FOSUserExtension();
        $config = $this->getEmptyConfig();
        $loader->load(array($config), $this->configuration);
        $this->assertTrue($this->configuration instanceof ContainerBuilder);
    }

    protected function createFullConfiguration()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new FOSUserExtension();
        $config = $this->getFullConfig();
        $loader->load(array($config), $this->configuration);
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
firewall_name: fos_user
user_class: Acme\MyBundle\Document\User
EOF;
        $parser = new Parser();

        return $parser->parse($yaml);
    }

    protected function getFullConfig()
    {
        $yaml = <<<EOF
db_driver: orm
firewall_name: fos_user
use_listener: true
use_flash_notifications: false
user_class: Acme\MyBundle\Entity\User
model_manager_name: custom
from_email:
    address: admin@acme.org
    sender_name: Acme Corp
profile:
    form:
        type: acme_my_profile
        name: acme_profile_form
        validation_groups: [acme_profile]
change_password:
    form:
        type: acme_my_change_password
        name: acme_change_password_form
        validation_groups: [acme_change_password]
registration:
    confirmation:
        from_email:
            address: register@acme.org
            sender_name: Acme Corp
        enabled: true
        template: AcmeMyBundle:Registration:mail.txt.twig
    form:
        type: acme_my_registration
        name: acme_registration_form
        validation_groups: [acme_registration]
resetting:
    token_ttl: 1800
    email:
        from_email:
            address: reset@acme.org
            sender_name: Acme Corp
        template: AcmeMyBundle:Resetting:mail.txt.twig
    form:
        type: acme_my_resetting
        name: acme_resetting_form
        validation_groups: [acme_resetting]
service:
    mailer: acme_my.mailer
    email_canonicalizer: acme_my.email_canonicalizer
    username_canonicalizer: acme_my.username_canonicalizer
    user_manager: acme_my.user_manager
group:
    group_class: Acme\MyBundle\Entity\Group
    form:
        type: acme_my_group
        name: acme_group_form
        validation_groups: [acme_group]
EOF;
        $parser = new Parser();

        return $parser->parse($yaml);
    }

    /**
     * @param string $value
     * @param string $key
     */
    private function assertAlias($value, $key)
    {
        $this->assertEquals($value, (string) $this->configuration->getAlias($key), sprintf('%s alias is correct', $key));
    }

    /**
     * @param mixed  $value
     * @param string $key
     */
    private function assertParameter($value, $key)
    {
        $this->assertEquals($value, $this->configuration->getParameter($key), sprintf('%s parameter is correct', $key));
    }

    /**
     * @param string $id
     */
    private function assertHasDefinition($id)
    {
        $this->assertTrue(($this->configuration->hasDefinition($id) ?: $this->configuration->hasAlias($id)));
    }

    /**
     * @param string $id
     */
    private function assertNotHasDefinition($id)
    {
        $this->assertFalse(($this->configuration->hasDefinition($id) ?: $this->configuration->hasAlias($id)));
    }

    protected function tearDown()
    {
        unset($this->configuration);
    }
}
