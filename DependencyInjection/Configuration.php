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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('fos_user');

        $supportedDrivers = array('orm', 'mongodb', 'couchdb', 'propel', 'custom');

        $rootNode
            ->children()
                ->scalarNode('db_driver')
                    ->validate()
                        ->ifNotInArray($supportedDrivers)
                        ->thenInvalid('The driver %s is not supported. Please choose one of '.json_encode($supportedDrivers))
                    ->end()
                    ->cannotBeOverwritten()
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('user_class')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('firewall_name')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('model_manager_name')->defaultNull()->end()
                ->booleanNode('use_listener')->defaultTrue()->end()
                ->booleanNode('use_username_form_type')->defaultTrue()->end()
                ->arrayNode('from_email')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('address')->defaultValue('webmaster@example.com')->cannotBeEmpty()->end()
                        ->scalarNode('sender_name')->defaultValue('webmaster')->cannotBeEmpty()->end()
                    ->end()
                ->end()
            ->end()
            // Using the custom driver requires changing the manager services
            ->validate()
                ->ifTrue(function($v){return 'custom' === $v['db_driver'] && 'fos_user.user_manager.default' === $v['service']['user_manager'];})
                ->thenInvalid('You need to specify your own user manager service when using the "custom" driver.')
            ->end()
            ->validate()
                ->ifTrue(function($v){return 'custom' === $v['db_driver'] && !empty($v['group']) && 'fos_user.group_manager.default' === $v['group']['group_manager'];})
                ->thenInvalid('You need to specify your own group manager service when using the "custom" driver.')
            ->end();

        $this->addProfileSection($rootNode);
        $this->addChangePasswordSection($rootNode);
        $this->addRegistrationSection($rootNode);
        $this->addResettingSection($rootNode);
        $this->addServiceSection($rootNode);
        $this->addTemplateSection($rootNode);
        $this->addGroupSection($rootNode);

        return $treeBuilder;
    }

    private function addProfileSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('profile')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->arrayNode('form')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('type')->defaultValue('fos_user_profile')->end()
                                ->scalarNode('handler')->defaultValue('fos_user.profile.form.handler.default')->end()
                                ->scalarNode('name')->defaultValue('fos_user_profile_form')->end()
                                ->arrayNode('validation_groups')
                                    ->prototype('scalar')->end()
                                    ->defaultValue(array('Profile', 'Default'))
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    private function addRegistrationSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('registration')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->arrayNode('confirmation')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')->defaultFalse()->end()
                                ->scalarNode('template')->defaultValue('FOSUserBundle:Registration:email.txt.twig')->end()
                                ->arrayNode('from_email')
                                    ->canBeUnset()
                                    ->children()
                                        ->scalarNode('address')->isRequired()->cannotBeEmpty()->end()
                                        ->scalarNode('sender_name')->isRequired()->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('form')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('type')->defaultValue('fos_user_registration')->end()
                                ->scalarNode('handler')->defaultValue('fos_user.registration.form.handler.default')->end()
                                ->scalarNode('name')->defaultValue('fos_user_registration_form')->end()
                                ->arrayNode('validation_groups')
                                    ->prototype('scalar')->end()
                                    ->defaultValue(array('Registration', 'Default'))
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    private function addResettingSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('resetting')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('token_ttl')->defaultValue(86400)->end()
                        ->arrayNode('email')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('template')->defaultValue('FOSUserBundle:Resetting:email.txt.twig')->end()
                                ->arrayNode('from_email')
                                    ->canBeUnset()
                                    ->children()
                                        ->scalarNode('address')->isRequired()->cannotBeEmpty()->end()
                                        ->scalarNode('sender_name')->isRequired()->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('form')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('type')->defaultValue('fos_user_resetting')->end()
                                ->scalarNode('handler')->defaultValue('fos_user.resetting.form.handler.default')->end()
                                ->scalarNode('name')->defaultValue('fos_user_resetting_form')->end()
                                ->arrayNode('validation_groups')
                                    ->prototype('scalar')->end()
                                    ->defaultValue(array('ResetPassword', 'Default'))
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    private function addChangePasswordSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('change_password')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->arrayNode('form')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('type')->defaultValue('fos_user_change_password')->end()
                                ->scalarNode('handler')->defaultValue('fos_user.change_password.form.handler.default')->end()
                                ->scalarNode('name')->defaultValue('fos_user_change_password_form')->end()
                                ->arrayNode('validation_groups')
                                    ->prototype('scalar')->end()
                                    ->defaultValue(array('ChangePassword', 'Default'))
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    private function addServiceSection(ArrayNodeDefinition $node)
    {
        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('service')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('mailer')->defaultValue('fos_user.mailer.default')->end()
                            ->scalarNode('email_canonicalizer')->defaultValue('fos_user.util.canonicalizer.default')->end()
                           ->scalarNode('token_generator')->defaultValue('fos_user.util.token_generator.default')->end()
                            ->scalarNode('username_canonicalizer')->defaultValue('fos_user.util.canonicalizer.default')->end()
                            ->scalarNode('user_manager')->defaultValue('fos_user.user_manager.default')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    private function addTemplateSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('template')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('engine')->defaultValue('twig')->end()
                    ->end()
                ->end()
            ->end();
    }

    private function addGroupSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('group')
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('group_class')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('group_manager')->defaultValue('fos_user.group_manager.default')->end()
                        ->arrayNode('form')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('type')->defaultValue('fos_user_group')->end()
                                ->scalarNode('handler')->defaultValue('fos_user.group.form.handler.default')->end()
                                ->scalarNode('name')->defaultValue('fos_user_group_form')->end()
                                ->arrayNode('validation_groups')
                                    ->prototype('scalar')->end()
                                    ->defaultValue(array('Registration', 'Default'))
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
