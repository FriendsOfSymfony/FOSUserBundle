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

        $rootNode
            ->children()
                ->scalarNode('db_driver')->cannotBeOverwritten()->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('firewall_name')->isRequired()->cannotBeEmpty()->end()
                ->booleanNode('use_listener')->defaultTrue()->end()
            ->end();

        $this->addClassSection($rootNode);
        $this->addServiceSection($rootNode);
        $this->addEncoderSection($rootNode);
        $this->addFormNameSection($rootNode);
        $this->addFormValidationGroupsSection($rootNode);
        $this->addEmailSection($rootNode);
        $this->addTemplateSection($rootNode);
        $this->addGroupSection($rootNode);

        return $treeBuilder;
    }

    private function addClassSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('class')
                    ->isRequired()
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('model')
                            ->isRequired()
                            ->children()
                                ->scalarNode('user')->isRequired()->cannotBeEmpty()->end()
                            ->end()
                        ->end()
                        ->arrayNode('form')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('profile')->defaultValue('FOS\\UserBundle\\Form\\ProfileFormType')->end()
                                ->scalarNode('registration')->defaultValue('FOS\\UserBundle\\Form\\RegistrationFormType')->end()
                                ->scalarNode('change_password')->defaultValue('FOS\\UserBundle\\Form\\ChangePasswordFormType')->end()
                                ->scalarNode('reset_password')->defaultValue('FOS\\UserBundle\\Form\\ResetPasswordFormType')->end()
                            ->end()
                        ->end()
                        ->arrayNode('form_handler')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('profile')->defaultValue('FOS\\UserBundle\\Form\\ProfileFormHandler')->end()
                                ->scalarNode('registration')->defaultValue('FOS\\UserBundle\\Form\\RegistrationFormHandler')->end()
                                ->scalarNode('change_password')->defaultValue('FOS\\UserBundle\\Form\\ChangePasswordFormHandler')->end()
                                ->scalarNode('reset_password')->defaultValue('FOS\\UserBundle\\Form\\ResetPasswordFormHandler')->end()
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
                            ->scalarNode('email_canonicalizer')->defaultValue('fos_user.util.email_canonicalizer.default')->end()
                            ->scalarNode('username_canonicalizer')->defaultValue('fos_user.util.username_canonicalizer.default')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    private function addEncoderSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('encoder')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('algorithm')->defaultValue('sha512')->end()
                        ->booleanNode('encode_as_base64')->defaultFalse()->end()
                        ->scalarNode('iterations')->defaultValue(1)->end()
                    ->end()
                ->end()
            ->end();
    }

    private function addFormNameSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('form_name')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('profile')
                            ->defaultValue('fos_user_profile_form')
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('registration')
                            ->defaultValue('fos_user_registration_form')
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('change_password')
                            ->defaultValue('fos_user_change_password_form')
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('reset_password')
                            ->defaultValue('fos_user_reset_password_form')
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    private function addFormValidationGroupsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('form_validation_groups')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('profile')
                            ->addDefaultsIfNotSet()
                            ->prototype('scalar')->end()
                            ->defaultValue(array('Profile'))
                        ->end()
                        ->arrayNode('registration')
                            ->addDefaultsIfNotSet()
                            ->prototype('scalar')->end()
                            ->defaultValue(array('Registration'))
                        ->end()
                        ->arrayNode('change_password')
                            ->addDefaultsIfNotSet()
                            ->prototype('scalar')->end()
                            ->defaultValue(array('ChangePassword'))
                        ->end()
                        ->arrayNode('reset_password')
                            ->addDefaultsIfNotSet()
                            ->prototype('scalar')->end()
                            ->defaultValue(array('ResetPassword'))
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    private function addEmailSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('email')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('from_email')
                            ->addDefaultsIfNotSet()
                            ->useAttributeAsKey('address')
                            ->prototype('scalar')
                                ->beforeNormalization()
                                    ->ifTrue(function ($v) { return is_array($v) && isset ($v['name']); })
                                    ->then(function ($v) { return $v['name']; })
                                ->end()
                            ->end()
                            ->defaultValue(array('webmaster@example.com' => 'webmaster'))
                        ->end()
                        ->arrayNode('confirmation')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')->defaultFalse()->end()
                                ->scalarNode('template')->defaultValue('FOSUserBundle:Registration:email.txt.twig')->end()
                            ->end()
                        ->end()
                        ->arrayNode('resetting_password')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('template')->defaultValue('FOSUserBundle:Resetting:email.txt.twig')->end()
                                ->scalarNode('token_ttl')->defaultValue(86400)->end()
                            ->end()
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
                        ->scalarNode('theme')->defaultValue('TwigBundle::form.html.twig')->end()
                    ->end()
                ->end()
            ->end();
    }

    private function addGroupSection(ArrayNodeDefinition $node)
    {
        $node
            ->canBeUnset()
            ->children()
                ->arrayNode('group')
                    ->children()
                        ->arrayNode('class')
                            ->isRequired()
                            ->children()
                                ->scalarNode('model')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('controller')->defaultValue('FOS\\UserBundle\\Controller\\GroupController')->end()
                            ->end()
                        ->end()
                        ->scalarNode('form')->defaultValue('FOS\\UserBundle\\Form\\GroupFormType')->end()
                        ->scalarNode('form_handler')->defaultValue('FOS\\UserBundle\\Form\\GroupFormHandler')->end()
                        ->scalarNode('form_name')
                            ->defaultValue('fos_user_group_form')
                            ->cannotBeEmpty()
                        ->end()
                        ->arrayNode('form_validation_groups')
                            ->addDefaultsIfNotSet()
                            ->prototype('scalar')->end()
                            ->defaultValue(array('Registration'))
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
