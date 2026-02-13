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

use FOS\UserBundle\DependencyInjection\FOSUserExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Parser;

class FOSUserExtensionTest extends TestCase
{
    /** @var ContainerBuilder */
    private $configuration;

    public function testUserLoadThrowsExceptionUnlessDatabaseDriverSet()
    {
        $this->expectException(InvalidConfigurationException::class);

        $loader = new FOSUserExtension();
        $config = $this->getEmptyConfig();
        unset($config['db_driver']);
        $loader->load([$config], new ContainerBuilder());
    }

    public function testUserLoadThrowsExceptionUnlessDatabaseDriverIsValid()
    {
        $this->expectException(InvalidConfigurationException::class);

        $loader = new FOSUserExtension();
        $config = $this->getEmptyConfig();
        $config['db_driver'] = 'foo';
        $loader->load([$config], new ContainerBuilder());
    }

    public function testUserLoadThrowsExceptionUnlessFirewallNameSet()
    {
        $this->expectException(InvalidConfigurationException::class);

        $loader = new FOSUserExtension();
        $config = $this->getEmptyConfig();
        unset($config['firewall_name']);
        $loader->load([$config], new ContainerBuilder());
    }

    public function testUserLoadThrowsExceptionUnlessUserModelClassSet()
    {
        $this->expectException(InvalidConfigurationException::class);

        $loader = new FOSUserExtension();
        $config = $this->getEmptyConfig();
        unset($config['user_class']);
        $loader->load([$config], new ContainerBuilder());
    }

    public function testCustomDriverWithoutManager()
    {
        $this->expectException(InvalidConfigurationException::class);

        $loader = new FOSUserExtension();
        $config = $this->getEmptyConfig();
        $config['db_driver'] = 'custom';
        $loader->load([$config], new ContainerBuilder());
    }

    public function testCustomDriver()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new FOSUserExtension();
        $config = $this->getEmptyConfig();
        $config['db_driver'] = 'custom';
        $config['service']['user_manager'] = 'acme.user_manager';
        $loader->load([$config], $this->configuration);

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
        $loader->load([$config], $this->configuration);
        $this->assertNotHasDefinition('fos_user.registration.form.factory');

        $mailer = $this->configuration->getDefinition('fos_user.mailer.twig_symfony');
        $parameters = $this->configuration->getParameterBag()->resolveValue(
            $mailer->getArgument(3)
        );
        $this->assertSame(
            [
                'confirmation' => ['address' => 'no-registration@acme.com', 'sender_name' => 'Acme Ltd'],
                'resetting' => ['address' => 'admin@acme.org', 'sender_name' => 'Acme Corp'],
            ],
            $parameters['from_email']
        );
    }

    public function testDisableResetting()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new FOSUserExtension();
        $config = $this->getEmptyConfig();
        $config['resetting'] = false;
        $loader->load([$config], $this->configuration);
        $this->assertNotHasDefinition('fos_user.resetting.form.factory');

        $mailer = $this->configuration->getDefinition('fos_user.mailer.twig_symfony');
        $parameters = $this->configuration->getParameterBag()->resolveValue(
            $mailer->getArgument(3)
        );
        $this->assertSame(
            [
                'confirmation' => ['address' => 'admin@acme.org', 'sender_name' => 'Acme Corp'],
                'resetting' => ['address' => 'no-resetting@acme.com', 'sender_name' => 'Acme Ltd'],
            ],
            $parameters['from_email']
        );
    }

    public function testDisableProfile()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new FOSUserExtension();
        $config = $this->getEmptyConfig();
        $config['profile'] = false;
        $loader->load([$config], $this->configuration);
        $this->assertNotHasDefinition('fos_user.profile.form.factory');
    }

    public function testDisableChangePassword()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new FOSUserExtension();
        $config = $this->getEmptyConfig();
        $config['change_password'] = false;
        $loader->load([$config], $this->configuration);
        $this->assertNotHasDefinition('fos_user.change_password.form.factory');
    }

    /**
     * @dataProvider providerEmailsDisabledFeature
     *
     * @param array<string, mixed>                        $testConfig
     * @param array{address: string, sender_name: string} $registration
     * @param array{address: string, sender_name: string} $resetting
     */
    public function testEmailsDisabledFeature(array $testConfig, array $registration, array $resetting)
    {
        $this->configuration = new ContainerBuilder();
        $loader = new FOSUserExtension();
        $config = $this->getEmptyConfig();
        $config = array_merge($config, $testConfig);
        $loader->load([$config], $this->configuration);

        $this->assertParameter($registration, 'fos_user.registration.confirmation.from_address');
        $this->assertParameter($resetting, 'fos_user.resetting.email.from_address');
    }

    /**
     * @return iterable<array{array<string, mixed>, array{address: string, sender_name: string}, array{address: string, sender_name: string}}>
     */
    public function providerEmailsDisabledFeature(): iterable
    {
        $configBothFeaturesDisabled = ['registration' => false, 'resetting' => false];
        $configResettingDisabled = ['resetting' => false];
        $configRegistrationDisabled = ['registration' => false];
        $configOverridenRegistrationEmail = [
            'registration' => [
                'confirmation' => [
                    'from_email' => ['address' => 'ltd@acme.com', 'sender_name' => 'Acme Ltd'],
                ],
            ],
        ];
        $configOverridenResettingEmail = [
            'resetting' => [
                'email' => [
                    'from_email' => ['address' => 'ltd@acme.com', 'sender_name' => 'Acme Ltd'],
                ],
            ],
        ];

        $default = ['address' => 'admin@acme.org', 'sender_name' => 'Acme Corp'];
        $overriden = ['address' => 'ltd@acme.com', 'sender_name' => 'Acme Ltd'];

        return [
            [$configBothFeaturesDisabled, ['address' => 'no-registration@acme.com', 'sender_name' => 'Acme Ltd'], ['address' => 'no-resetting@acme.com', 'sender_name' => 'Acme Ltd']],
            [$configResettingDisabled, $default, ['address' => 'no-resetting@acme.com', 'sender_name' => 'Acme Ltd']],
            [$configRegistrationDisabled, ['address' => 'no-registration@acme.com', 'sender_name' => 'Acme Ltd'], $default],
            [$configOverridenRegistrationEmail, $overriden, $default],
            [$configOverridenResettingEmail, $default, $overriden],
        ];
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
    }

    public function testUserLoadManagerClass()
    {
        $this->createFullConfiguration();

        $this->assertParameter('orm', 'fos_user.storage');
        $this->assertParameter('custom', 'fos_user.model_manager_name');
        $this->assertAlias('acme_my.user_manager', 'fos_user.user_manager');
    }

    public function testUserLoadFormClass()
    {
        $this->createFullConfiguration();

        $this->assertParameter('acme_my_profile', 'fos_user.profile.form.type');
        $this->assertParameter('acme_my_registration', 'fos_user.registration.form.type');
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
        $this->assertParameter('acme_change_password_form', 'fos_user.change_password.form.name');
        $this->assertParameter('acme_resetting_form', 'fos_user.resetting.form.name');
    }

    public function testUserLoadFormServiceWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertHasDefinition('fos_user.profile.form.factory');
        $this->assertHasDefinition('fos_user.registration.form.factory');
        $this->assertHasDefinition('fos_user.change_password.form.factory');
        $this->assertHasDefinition('fos_user.resetting.form.factory');
    }

    public function testUserLoadFormService()
    {
        $this->createFullConfiguration();

        $this->assertHasDefinition('fos_user.profile.form.factory');
        $this->assertHasDefinition('fos_user.registration.form.factory');
        $this->assertHasDefinition('fos_user.change_password.form.factory');
        $this->assertHasDefinition('fos_user.resetting.form.factory');
    }

    public function testUserLoadConfirmationEmailWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter(false, 'fos_user.registration.confirmation.enabled');
        $this->assertParameter(['address' => 'admin@acme.org', 'sender_name' => 'Acme Corp'], 'fos_user.registration.confirmation.from_address');
        $this->assertParameter('@FOSUser/Registration/email.txt.twig', 'fos_user.registration.confirmation.template');
        $this->assertParameter('@FOSUser/Resetting/email.txt.twig', 'fos_user.resetting.email.template');
        $this->assertParameter(['address' => 'admin@acme.org', 'sender_name' => 'Acme Corp'], 'fos_user.resetting.email.from_address');
        $this->assertParameter(86400, 'fos_user.resetting.token_ttl');
    }

    public function testUserLoadConfirmationEmail()
    {
        $this->createFullConfiguration();

        $this->assertParameter(true, 'fos_user.registration.confirmation.enabled');
        $this->assertParameter(['address' => 'register@acme.org', 'sender_name' => 'Acme Corp'], 'fos_user.registration.confirmation.from_address');
        $this->assertParameter('AcmeMyBundle:Registration:mail.txt.twig', 'fos_user.registration.confirmation.template');
        $this->assertParameter('AcmeMyBundle:Resetting:mail.txt.twig', 'fos_user.resetting.email.template');
        $this->assertParameter(['address' => 'reset@acme.org', 'sender_name' => 'Acme Corp'], 'fos_user.resetting.email.from_address');
        $this->assertParameter(7200, 'fos_user.resetting.retry_ttl');
    }

    public function testUserLoadUtilServiceWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertAlias('fos_user.mailer.noop', 'fos_user.mailer');
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
    public function testUserManagerSetFactory(string $dbDriver, string $doctrineService)
    {
        $this->configuration = new ContainerBuilder();
        $loader = new FOSUserExtension();
        $config = $this->getEmptyConfig();
        $config['db_driver'] = $dbDriver;
        $loader->load([$config], $this->configuration);

        $definition = $this->configuration->getDefinition('fos_user.object_manager');

        $this->assertAlias($doctrineService, 'fos_user.doctrine_registry');

        $factory = $definition->getFactory();

        $this->assertIsArray($factory);
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $factory[0]);
        $this->assertSame('fos_user.doctrine_registry', (string) $factory[0]);
        $this->assertSame('getManager', $factory[1]);
    }

    /**
     * @return iterable<array{string, string}>
     */
    public function userManagerSetFactoryProvider(): iterable
    {
        return [
            ['orm', 'doctrine'],
            ['mongodb', 'doctrine_mongodb'],
        ];
    }

    protected function createEmptyConfiguration(): void
    {
        $this->configuration = new ContainerBuilder();
        $loader = new FOSUserExtension();
        $config = $this->getEmptyConfig();
        $loader->load([$config], $this->configuration);
    }

    protected function createFullConfiguration(): void
    {
        $this->configuration = new ContainerBuilder();
        $loader = new FOSUserExtension();
        $config = $this->getFullConfig();
        $loader->load([$config], $this->configuration);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getEmptyConfig(): array
    {
        $yaml = <<<EOF
db_driver: mongodb
firewall_name: fos_user
user_class: Acme\MyBundle\Document\User
from_email:
    address: admin@acme.org
    sender_name: Acme Corp
service:
    mailer: fos_user.mailer.noop
EOF;
        $parser = new Parser();

        return $parser->parse($yaml);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getFullConfig(): array
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
    retry_ttl: 7200
    token_ttl: 86400
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
EOF;
        $parser = new Parser();

        return $parser->parse($yaml);
    }

    /**
     * @param string $value
     * @param string $key
     */
    private function assertAlias($value, $key): void
    {
        $this->assertSame($value, (string) $this->configuration->getAlias($key), sprintf('%s alias is correct', $key));
    }

    /**
     * @param mixed  $value
     * @param string $key
     */
    private function assertParameter($value, $key): void
    {
        $this->assertSame($value, $this->configuration->getParameter($key), sprintf('%s parameter is correct', $key));
    }

    /**
     * @param string $id
     */
    private function assertHasDefinition($id): void
    {
        $this->assertTrue($this->configuration->hasDefinition($id) ?: $this->configuration->hasAlias($id));
    }

    /**
     * @param string $id
     */
    private function assertNotHasDefinition($id): void
    {
        $this->assertFalse($this->configuration->hasDefinition($id) ?: $this->configuration->hasAlias($id));
    }
}
