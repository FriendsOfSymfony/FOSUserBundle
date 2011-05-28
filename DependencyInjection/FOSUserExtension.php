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

        if (!in_array(strtolower($config['db_driver']), array('orm', 'mongodb'))) {
            throw new \InvalidArgumentException(sprintf('Invalid db driver "%s".', $config['db_driver']));
        }
        $loader->load(sprintf('%s.xml', $config['db_driver']));

        foreach (array('services', 'form', 'validator', 'security', 'util', 'mailer', 'listener') as $basename) {
            $loader->load(sprintf('%s.xml', $basename));
        }

        $container->setAlias('fos_user.mailer', $config['service']['mailer']);
        $container->setAlias('fos_user.util.email_canonicalizer', $config['service']['email_canonicalizer']);
        $container->setAlias('fos_user.util.username_canonicalizer', $config['service']['username_canonicalizer']);

        if (!empty($config['group'])) {
            $loader->load('group.xml');
            $loader->load(sprintf('%s_group.xml', $config['db_driver']));
            $this->remapParametersNamespaces($config['group'], $container, array(
                'class' => 'fos_user.%s.group.class',
                '' => array(
                    'form' => 'fos_user.form.type.group.class',
                    'form_handler' => 'fos_user.form.handler.group.class',
                    'form_name' => 'fos_user.form.group.name',
                    'form_validation_groups' => 'fos_user.form.group.validation_groups'
                ),
            ));
        }

        if ($config['use_listener']) {
            switch ($config['db_driver']) {
                case 'orm':
                    $container->getDefinition('fos_user.user_listener')->addTag('doctrine.event_subscriber');
                    break;

                case 'mongodb':
                    $container->getDefinition('fos_user.user_listener')->addTag('doctrine.common.event_subscriber');
                    break;

                default:
                    break;
            }
        }

        $this->remapParametersNamespaces($config, $container, array(
            ''          => array(
                'firewall_name' => 'fos_user.firewall_name'
            ),
            'encoder'   => 'fos_user.encoder.%s',
            'template'  => 'fos_user.template.%s',
            'form_name' => 'fos_user.form.%s.name',
            'form_validation_groups' => 'fos_user.form.%s.validation_groups',
        ));

        $this->remapParametersNamespaces($config['class'], $container, array(
            'model'         => 'fos_user.model.%s.class',
            'form'          => 'fos_user.form.type.%s.class',
            'form_handler'  => 'fos_user.form.handler.%s.class',
        ));

        $this->remapParametersNamespaces($config['email'], $container, array(
            ''                   => array('from_email' => 'fos_user.email.from_email'),
            'confirmation'       => 'fos_user.email.confirmation.%s',
            'resetting_password' => 'fos_user.email.resetting_password.%s',
        ));
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
