<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\DependencyInjection;

use FOS\UserBundle\Mailer\MailerInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @internal
 */
final class FOSUserExtension extends Extension
{
    /**
     * @var array<string, array{registry: string, tag: string}>
     */
    private static $doctrineDrivers = [
        'orm' => [
            'registry' => 'doctrine',
            'tag' => 'doctrine.event_listener',
        ],
        'mongodb' => [
            'registry' => 'doctrine_mongodb',
            'tag' => 'doctrine_mongodb.odm.event_listener',
        ],
    ];

    private bool $mailerNeeded = false;
    private bool $sessionNeeded = false;

    public function load(array $configs, ContainerBuilder $container): void
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        if ('custom' !== $config['db_driver']) {
            if (isset(self::$doctrineDrivers[$config['db_driver']])) {
                $loader->load('doctrine.php');
                $container->setAlias('fos_user.doctrine_registry', new Alias(self::$doctrineDrivers[$config['db_driver']]['registry'], false));
            } else {
                $loader->load(sprintf('%s.php', $config['db_driver']));
            }
            $container->setParameter($this->getAlias().'.backend_type_'.$config['db_driver'], true);
        }

        if (isset(self::$doctrineDrivers[$config['db_driver']])) {
            $definition = $container->getDefinition('fos_user.object_manager');
            $definition->setFactory([new Reference('fos_user.doctrine_registry'), 'getManager']);
        }

        foreach (['validator', 'security', 'util', 'mailer', 'listeners', 'commands'] as $basename) {
            $loader->load(sprintf('%s.php', $basename));
        }

        if (!$config['use_authentication_listener']) {
            $container->removeDefinition('fos_user.listener.authentication');
        }

        if (!$config['register_last_login']) {
            $container->removeDefinition('fos_user.security.interactive_login_listener');
        }

        if ($config['use_flash_notifications']) {
            $this->sessionNeeded = true;
            $loader->load('flash_notifications.php');
        }

        $container->setAlias('fos_user.util.email_canonicalizer', $config['service']['email_canonicalizer']);
        $container->setAlias('fos_user.util.username_canonicalizer', $config['service']['username_canonicalizer']);
        $container->setAlias('fos_user.util.token_generator', $config['service']['token_generator']);
        $container->setAlias('fos_user.user_manager', new Alias($config['service']['user_manager'], true));

        if ($config['use_listener'] && isset(self::$doctrineDrivers[$config['db_driver']])) {
            $listenerDefinition = $container->getDefinition('fos_user.user_listener');
            $listenerDefinition->addTag(self::$doctrineDrivers[$config['db_driver']]['tag'], ['event' => 'prePersist']);
            $listenerDefinition->addTag(self::$doctrineDrivers[$config['db_driver']]['tag'], ['event' => 'preUpdate']);
        }

        if ($config['use_username_form_type']) {
            $loader->load('username_form_type.php');
        }

        $this->remapParametersNamespaces($config, $container, [
            '' => [
                'db_driver' => 'fos_user.storage',
                'firewall_name' => 'fos_user.firewall_name',
                'model_manager_name' => 'fos_user.model_manager_name',
                'user_class' => 'fos_user.model.user.class',
            ],
        ]);

        if (!empty($config['profile'])) {
            $this->loadProfile($config['profile'], $container, $loader);
        }

        if (!empty($config['registration'])) {
            $this->loadRegistration($config['registration'], $container, $loader, $config['from_email']);
        }

        if (!empty($config['change_password'])) {
            $this->loadChangePassword($config['change_password'], $container, $loader);
        }

        if (!empty($config['resetting'])) {
            $this->loadResetting($config['resetting'], $container, $loader, $config['from_email']);
        }

        if ($this->mailerNeeded && null === $config['service']['mailer']) {
            throw new \LogicException('Configuring the mailer service for FOSUserBundle is mandatory when enabling features that need the mailer.');
        }

        if (null !== $config['service']['mailer']) {
            $container->setAlias('fos_user.mailer', $config['service']['mailer']);
            $container->setAlias(MailerInterface::class, new Alias('fos_user.mailer', false));
        }

        if ($this->sessionNeeded) {
            $container->setParameter('fos_user.session_needed', true);
        }
    }

    public function getNamespace(): string
    {
        return 'http://friendsofsymfony.github.io/schema/dic/user';
    }

    /**
     * /**
     * @param array<string, mixed>  $config
     * @param array<string, string> $map
     */
    protected function remapParameters(array $config, ContainerBuilder $container, array $map): void
    {
        foreach ($map as $name => $paramName) {
            if (array_key_exists($name, $config)) {
                $container->setParameter($paramName, $config[$name]);
            }
        }
    }

    /**
     * @param array<string, mixed>                        $config
     * @param array<string, string|array<string, string>> $namespaces
     */
    protected function remapParametersNamespaces(array $config, ContainerBuilder $container, array $namespaces): void
    {
        foreach ($namespaces as $ns => $map) {
            if ($ns) {
                if (!array_key_exists($ns, $config)) {
                    continue;
                }
                $namespaceConfig = $config[$ns];
            } else {
                $namespaceConfig = $config;
            }
            if (is_array($map)) {
                $this->remapParameters($namespaceConfig, $container, $map);
            } else {
                foreach ($namespaceConfig as $name => $value) {
                    $container->setParameter(sprintf($map, $name), $value);
                }
            }
        }
    }

    /**
     * @param array<string, mixed> $config
     */
    private function loadProfile(array $config, ContainerBuilder $container, PhpFileLoader $loader): void
    {
        $loader->load('profile.php');

        $this->remapParametersNamespaces($config, $container, [
            'form' => 'fos_user.profile.form.%s',
        ]);
    }

    /**
     * @param array<string, mixed>                        $config
     * @param array{address: string, sender_name: string} $fromEmail
     */
    private function loadRegistration(array $config, ContainerBuilder $container, PhpFileLoader $loader, array $fromEmail): void
    {
        $loader->load('registration.php');
        $this->sessionNeeded = true;

        if ($config['confirmation']['enabled']) {
            $this->mailerNeeded = true;
            $loader->load('email_confirmation.php');
        }

        if (isset($config['confirmation']['from_email'])) {
            // overwrite the global one
            $fromEmail = $config['confirmation']['from_email'];
            unset($config['confirmation']['from_email']);
        }
        $container->setParameter('fos_user.registration.confirmation.from_address', $fromEmail);

        $this->remapParametersNamespaces($config, $container, [
            'confirmation' => 'fos_user.registration.confirmation.%s',
            'form' => 'fos_user.registration.form.%s',
        ]);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function loadChangePassword(array $config, ContainerBuilder $container, PhpFileLoader $loader): void
    {
        $loader->load('change_password.php');

        $this->remapParametersNamespaces($config, $container, [
            'form' => 'fos_user.change_password.form.%s',
        ]);
    }

    /**
     * @param array<string, mixed>                        $config
     * @param array{address: string, sender_name: string} $fromEmail
     */
    private function loadResetting(array $config, ContainerBuilder $container, PhpFileLoader $loader, array $fromEmail): void
    {
        $this->mailerNeeded = true;
        $loader->load('resetting.php');

        if (isset($config['email']['from_email'])) {
            // overwrite the global one
            $fromEmail = $config['email']['from_email'];
            unset($config['email']['from_email']);
        }
        $container->setParameter('fos_user.resetting.email.from_address', $fromEmail);

        $this->remapParametersNamespaces($config, $container, [
            '' => [
                'retry_ttl' => 'fos_user.resetting.retry_ttl',
                'token_ttl' => 'fos_user.resetting.token_ttl',
            ],
            'email' => 'fos_user.resetting.email.%s',
            'form' => 'fos_user.resetting.form.%s',
        ]);
    }
}
