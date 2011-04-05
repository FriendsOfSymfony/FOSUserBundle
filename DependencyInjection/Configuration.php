<?php

namespace FOS\UserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * This class contains the configuration information for the bundle
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class Configuration
{
    /**
     * Generates the configuration tree.
     *
     * @return \Symfony\Component\DependencyInjection\Configuration\NodeInterface
     */
    public function getConfigTree()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('fos_user');

        $rootNode
            ->children()
                ->scalarNode('db_driver')->cannotBeOverwritten()->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('firewall_name')->isRequired()->cannotBeEmpty()->end()
            ->end();

        $this->addClassSection($rootNode);
        $this->addServiceSection($rootNode);
        $this->addEncoderSection($rootNode);
        $this->addFormNameSection($rootNode);
        $this->addFormValidationGroupsSection($rootNode);
        $this->addEmailSection($rootNode);
        $this->addTemplateSection($rootNode);
        $this->addGroupSection($rootNode);

        return $treeBuilder->buildTree();
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
                                ->scalarNode('user')->defaultValue('FOS\\UserBundle\\Form\\UserForm')->end()
                                ->scalarNode('change_password')->defaultValue('FOS\\UserBundle\\Form\\ChangePasswordForm')->end()
                                ->scalarNode('reset_password')->defaultValue('FOS\\UserBundle\\Form\\ResetPasswordForm')->end()
                            ->end()
                        ->end()
                        ->arrayNode('controller')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('user')->defaultValue('FOS\\UserBundle\\Controller\\UserController')->end()
                                ->scalarNode('security')->defaultValue('FOS\\UserBundle\\Controller\\SecurityController')->end()
                            ->end()
                        ->end()
                        ->arrayNode('util')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('email_canonicalizer')->defaultValue('FOS\\UserBundle\\Util\\Canonicalizer')->end()
                                ->scalarNode('username_canonicalizer')->defaultValue('FOS\\UserBundle\\Util\\Canonicalizer')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    private function addServiceSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('service')
                    ->children()
                        ->arrayNode('util')
                            ->children()
                                ->scalarNode('mailer')->end()
                            ->end()
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
                        ->scalarNode('user')
                            ->defaultValue('fos_user_user_form')
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
                        ->arrayNode('user')
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
                                ->scalarNode('template')->defaultValue('FOSUserBundle:User:confirmationEmail')->end()
                            ->end()
                        ->end()
                        ->arrayNode('resetting_password')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('template')->defaultValue('FOSUserBundle:User:resettingPasswordEmail')->end()
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
                        ->scalarNode('bundle')->defaultValue('FOSUserBundle')->end()
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
                                ->scalarNode('form')->defaultValue('FOS\\UserBundle\\Form\\GroupForm')->end()
                                ->scalarNode('controller')->defaultValue('FOS\\UserBundle\\Controller\\GroupController')->end()
                            ->end()
                        ->end()
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
