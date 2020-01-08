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

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class FOSUserExtension extends Extension
{
    /**
     * @var array
     */
    private static $doctrineDrivers = [
        'orm' => [
            'registry' => 'doctrine',
            'tag' => 'doctrine.event_subscriber',
        ],
        'mongodb' => [
            'registry' => 'doctrine_mongodb',
            'tag' => 'doctrine_mongodb.odm.event_subscriber',
        ],
        'couchdb' => [
            'registry' => 'doctrine_couchdb',
            'tag' => 'doctrine_couchdb.event_subscriber',
            'listener_class' => 'FOS\UserBundle\Doctrine\CouchDB\UserListener',
        ],
    ];

    private $mailerNeeded = false;
    private $sessionNeeded = false;

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        if ('custom' !== $config['db_driver']) {
            if (isset(self::$doctrineDrivers[$config['db_driver']])) {
                $loader->load('doctrine.xml');
                $container->setAlias('fos_user.doctrine_registry', new Alias(self::$doctrineDrivers[$config['db_driver']]['registry'], false));
            } else {
                $loader->load(sprintf('%s.xml', $config['db_driver']));
            }
            $container->setParameter($this->getAlias().'.backend_type_'.$config['db_driver'], true);
        }

        if (isset(self::$doctrineDrivers[$config['db_driver']])) {
            $definition = $container->getDefinition('fos_user.object_manager');
            $definition->setFactory([new Reference('fos_user.doctrine_registry'), 'getManager']);
        }

        foreach (['validator', 'security', 'util', 'mailer', 'listeners', 'commands'] as $basename) {
            $loader->load(sprintf('%s.xml', $basename));
        }

        if (!$config['use_authentication_listener']) {
            $container->removeDefinition('fos_user.listener.authentication');
        }

        if ($config['use_flash_notifications']) {
            $this->sessionNeeded = true;
            $loader->load('flash_notifications.xml');
        }

        $container->setAlias('fos_user.util.email_canonicalizer', $config['service']['email_canonicalizer']);
        $container->setAlias('fos_user.util.username_canonicalizer', $config['service']['username_canonicalizer']);
        $container->setAlias('fos_user.util.token_generator', $config['service']['token_generator']);
        $container->setAlias('fos_user.user_manager', new Alias($config['service']['user_manager'], true));

        if ($config['use_listener'] && isset(self::$doctrineDrivers[$config['db_driver']])) {
            $listenerDefinition = $container->getDefinition('fos_user.user_listener');
            $listenerDefinition->addTag(self::$doctrineDrivers[$config['db_driver']]['tag']);
            if (isset(self::$doctrineDrivers[$config['db_driver']]['listener_class'])) {
                $listenerDefinition->setClass(self::$doctrineDrivers[$config['db_driver']]['listener_class']);
            }
        }

        if ($config['use_username_form_type']) {
            $loader->load('username_form_type.xml');
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

        if (!empty($config['group'])) {
            $this->loadGroups($config['group'], $container, $loader, $config['db_driver']);
        }

        if ($this->mailerNeeded) {
            $container->setAlias('fos_user.mailer', $config['service']['mailer']);
        }

        if ($this->sessionNeeded) {
            // Use a private alias rather than a parameter, to avoid leaking it at runtime (the private alias will be removed)
            $container->setAlias('fos_user.session', new Alias('session', false));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getNamespace()
    {
        return 'http://friendsofsymfony.github.io/schema/dic/user';
    }

    protected function remapParameters(array $config, ContainerBuilder $container, array $map)
    {
        foreach ($map as $name => $paramName) {
            if (array_key_exists($name, $config)) {
                $container->setParameter($paramName, $config[$name]);
            }
        }
    }

    protected function remapParametersNamespaces(array $config, ContainerBuilder $container, array $namespaces)
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

    private function loadProfile(array $config, ContainerBuilder $container, XmlFileLoader $loader)
    {
        $loader->load('profile.xml');

        $this->remapParametersNamespaces($config, $container, [
            'form' => 'fos_user.profile.form.%s',
        ]);
    }

    private function loadRegistration(array $config, ContainerBuilder $container, XmlFileLoader $loader, array $fromEmail)
    {
        $loader->load('registration.xml');
        $this->sessionNeeded = true;

        if ($config['confirmation']['enabled']) {
            $this->mailerNeeded = true;
            $loader->load('email_confirmation.xml');
        }

        if (isset($config['confirmation']['from_email'])) {
            // overwrite the global one
            $fromEmail = $config['confirmation']['from_email'];
            unset($config['confirmation']['from_email']);
        }
        $container->setParameter('fos_user.registration.confirmation.from_email', [$fromEmail['address'] => $fromEmail['sender_name']]);

        $this->remapParametersNamespaces($config, $container, [
            'confirmation' => 'fos_user.registration.confirmation.%s',
            'form' => 'fos_user.registration.form.%s',
        ]);
    }

    private function loadChangePassword(array $config, ContainerBuilder $container, XmlFileLoader $loader)
    {
        $loader->load('change_password.xml');

        $this->remapParametersNamespaces($config, $container, [
            'form' => 'fos_user.change_password.form.%s',
        ]);
    }

    private function loadResetting(array $config, ContainerBuilder $container, XmlFileLoader $loader, array $fromEmail)
    {
        $this->mailerNeeded = true;
        $loader->load('resetting.xml');

        if (isset($config['email']['from_email'])) {
            // overwrite the global one
            $fromEmail = $config['email']['from_email'];
            unset($config['email']['from_email']);
        }
        $container->setParameter('fos_user.resetting.email.from_email', [$fromEmail['address'] => $fromEmail['sender_name']]);

        $this->remapParametersNamespaces($config, $container, [
            '' => [
                'retry_ttl' => 'fos_user.resetting.retry_ttl',
                'token_ttl' => 'fos_user.resetting.token_ttl',
            ],
            'email' => 'fos_user.resetting.email.%s',
            'form' => 'fos_user.resetting.form.%s',
        ]);
    }

    /**
     * @param string $dbDriver
     */
    private function loadGroups(array $config, ContainerBuilder $container, XmlFileLoader $loader, $dbDriver)
    {
        $loader->load('group.xml');
        if ('custom' !== $dbDriver) {
            if (isset(self::$doctrineDrivers[$dbDriver])) {
                $loader->load('doctrine_group.xml');
            } else {
                $loader->load(sprintf('%s_group.xml', $dbDriver));
            }
        }

        $container->setAlias('fos_user.group_manager', new Alias($config['group_manager'], true));
        $container->setAlias('FOS\UserBundle\Model\GroupManagerInterface', new Alias('fos_user.group_manager', false));

        $this->remapParametersNamespaces($config, $container, [
            '' => [
                'group_class' => 'fos_user.model.group.class',
            ],
            'form' => 'fos_user.group.form.%s',
        ]);
    }
}
