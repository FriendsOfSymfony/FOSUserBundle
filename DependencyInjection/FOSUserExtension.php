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
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class FOSUserExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        if (!in_array(strtolower($config['db_driver']), array('orm', 'mongodb', 'couchdb'))) {
            throw new \InvalidArgumentException(sprintf('Invalid db driver "%s".', $config['db_driver']));
        }
        $loader->load(sprintf('%s.xml', $config['db_driver']));

        foreach (array('validator', 'security', 'util', 'mailer') as $basename) {
            $loader->load(sprintf('%s.xml', $basename));
        }

        $container->setAlias('fos_user.mailer', $config['service']['mailer']);
        $container->setAlias('fos_user.util.email_canonicalizer', $config['service']['email_canonicalizer']);
        $container->setAlias('fos_user.util.username_canonicalizer', $config['service']['username_canonicalizer']);
        $container->setAlias('fos_user.user_manager', $config['service']['user_manager']);

        if ($config['use_listener']) {
            switch ($config['db_driver']) {
                case 'orm':
                    $container->getDefinition('fos_user.user_listener')->addTag('doctrine.event_subscriber');
                    break;

                case 'mongodb':
                    $container->getDefinition('fos_user.user_listener')->addTag('doctrine.common.event_subscriber');
                    break;

                case 'couchdb':
                    $container->getDefinition('fos_user.user_listener')->addTag('doctrine_couchdb.event_subscriber');
                    break;

                default:
                    break;
            }
        }

        $this->remapParametersNamespaces($config, $container, array(
            ''          => array(
                'firewall_name' => 'fos_user.firewall_name',
                'user_class' => 'fos_user.model.user.class',
            ),
            'encoder'   => 'fos_user.encoder.%s',
            'template'  => 'fos_user.template.%s',
        ));
        $container->setParameter('fos_user.registration.confirmation.from_email', $config['from_email']);
        $container->setParameter('fos_user.resetting.email.from_email', $config['from_email']);

        if (!empty($config['profile'])) {
            $loader->load('profile.xml');

            $container->setParameter('fos_user.profile.form.validation_groups', $config['profile']['form']['validation_groups']);
            unset($config['profile']['form']['validation_groups']);
            $container->setParameter('fos_user.profile.form.name', $config['profile']['form']['name']);
            unset($config['profile']['form']['name']);

            $this->remapParametersNamespaces($config['profile'], $container, array(
                'form' => 'fos_user.profile.form.%s.class',
            ));
        }

        if (!empty($config['registration'])) {
            $loader->load('registration.xml');

            $container->setParameter('fos_user.registration.form.validation_groups', $config['registration']['form']['validation_groups']);
            unset($config['registration']['form']['validation_groups']);
            $container->setParameter('fos_user.registration.form.name', $config['registration']['form']['name']);
            unset($config['registration']['form']['name']);

            if (!empty($config['registration']['confirmation']['from_email'])) {
                $container->setParameter('fos_user.registration.confirmation.from_email', $config['registration']['confirmation']['from_email']);
            }
            unset($config['registration']['confirmation']['from_email']);

            $this->remapParametersNamespaces($config['registration'], $container, array(
                'confirmation' => 'fos_user.registration.confirmation.%s',
                'form' => 'fos_user.registration.form.%s.class',
            ));
        }

        if (!empty($config['change_password'])) {
            $loader->load('change_password.xml');

            $container->setParameter('fos_user.change_password.form.validation_groups', $config['change_password']['form']['validation_groups']);
            unset($config['change_password']['form']['validation_groups']);
            $container->setParameter('fos_user.change_password.form.name', $config['change_password']['form']['name']);
            unset($config['change_password']['form']['name']);

            $this->remapParametersNamespaces($config['change_password'], $container, array(
                'form' => 'fos_user.change_password.form.%s.class',
            ));
        }

        if (!empty($config['resetting'])) {
            $loader->load('resetting.xml');

            $container->setParameter('fos_user.resetting.form.validation_groups', $config['resetting']['form']['validation_groups']);
            unset($config['resetting']['form']['validation_groups']);
            $container->setParameter('fos_user.resetting.form.name', $config['resetting']['form']['name']);
            unset($config['resetting']['form']['name']);

            if (!empty($config['resetting']['email']['from_email'])) {
                $container->setParameter('fos_user.resetting.email.from_email', $config['resetting']['email']['from_email']);
            }
            unset($config['resetting']['email']['from_email']);

            $this->remapParametersNamespaces($config['resetting'], $container, array(
                '' => array (
                    'token_ttl' => 'fos_user.resetting.token_ttl',
                ),
                'email' => 'fos_user.resetting.email.%s',
                'form' => 'fos_user.resetting.form.%s.class',
            ));
        }

        if (!empty($config['group'])) {
            $loader->load('group.xml');
            $loader->load(sprintf('%s_group.xml', $config['db_driver']));

            $container->setParameter('fos_user.form.group.validation_groups', $config['group']['form']['validation_groups']);
            unset($config['group']['form']['validation_groups']);
            $container->setParameter('fos_user.form.group.name', $config['group']['form']['name']);
            unset($config['group']['form']['name']);

            $this->remapParametersNamespaces($config['group'], $container, array(
                '' => array(
                    'group_class' => 'fos_user.model.group.class',
                ),
                'form' => 'fos_user.form.%s.group.class',
            ));
        }
    }

    protected function remapParameters(array $config, ContainerBuilder $container, array $map)
    {
        foreach ($map as $name => $paramName) {
            if (isset($config[$name])) {
                $container->setParameter($paramName, $config[$name]);
            }
        }
    }

    protected function remapParametersNamespaces(array $config, ContainerBuilder $container, array $namespaces)
    {
        foreach ($namespaces as $ns => $map) {
            if ($ns) {
                if (!isset($config[$ns])) {
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
                    if (null !== $value) {
                        $container->setParameter(sprintf($map, $name), $value);
                    }
                }
            }
        }
    }
}
