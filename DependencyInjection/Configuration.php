<?php

namespace FOS\UserBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Configuration\Builder\NodeBuilder;
use Symfony\Component\DependencyInjection\Configuration\Builder\TreeBuilder;

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
        $rootNode = $treeBuilder->root('fos_user:config', 'array');

        $rootNode
            ->scalarNode('db_driver')->cannotBeOverwritten()->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('provider_key')->isRequired()->cannotBeEmpty()->end();

        $this->addClassSection($rootNode);
        $this->addEncoderSection($rootNode);
        $this->addFormNameSection($rootNode);
        $this->addFormValidationGroupsSection($rootNode);
        $this->addEmailSection($rootNode);
        $this->addTemplateSection($rootNode);

        return $treeBuilder->buildTree();
    }

    private function addClassSection(NodeBuilder $node)
    {
        $node
            ->arrayNode('class')
                ->isRequired()
                ->addDefaultsIfNotSet()
                ->arrayNode('model')
                    ->isRequired()
                    ->scalarNode('user')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('group')->isRequired()->cannotBeEmpty()->end()
                ->end()
                ->arrayNode('form')
                    ->addDefaultsIfNotSet()
                    ->scalarNode('user')->defaultValue('FOS\\UserBundle\\Form\\UserForm')->end()
                    ->scalarNode('group')->defaultValue('FOS\\UserBundle\\Form\\GroupForm')->end()
                    ->scalarNode('change_password')->defaultValue('FOS\\UserBundle\\Form\\ChangePasswordForm')->end()
                    ->scalarNode('reset_password')->defaultValue('FOS\\UserBundle\\Form\\ResetPasswordForm')->end()
                ->end()
                ->arrayNode('controller')
                    ->addDefaultsIfNotSet()
                    ->scalarNode('user')->defaultValue('FOS\\UserBundle\\Controller\\UserController')->end()
                    ->scalarNode('group')->defaultValue('FOS\\UserBundle\\Controller\\GroupController')->end()
                    ->scalarNode('security')->defaultValue('FOS\\UserBundle\\Controller\\SecurityController')->end()
                ->end()
                ->arrayNode('util')
                    ->addDefaultsIfNotSet()
                    ->scalarNode('email_canonicalizer')->defaultValue('FOS\\UserBundle\\Util\\Canonicalizer')->end()
                    ->scalarNode('username_canonicalizer')->defaultValue('FOS\\UserBundle\\Util\\Canonicalizer')->end()
                ->end()
            ->end();
    }

    private function addEncoderSection(NodeBuilder $node)
    {
        $node
            ->arrayNode('encoder')
                ->addDefaultsIfNotSet()
                ->scalarNode('algorithm')->defaultValue('sha512')->end()
                ->booleanNode('encode_as_base64')->defaultFalse()->end()
                ->scalarNode('iterations')->defaultValue(1)->end()
            ->end();
    }

    private function addFormNameSection(NodeBuilder $node)
    {
        $node
            ->arrayNode('form_name')
                ->addDefaultsIfNotSet()
                ->scalarNode('user')
                    ->defaultValue('fos_user_user_form')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('group')
                    ->defaultValue('fos_user_group_form')
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
            ->end();
    }

    private function addFormValidationGroupsSection(NodeBuilder $node)
    {
        $node
            ->arrayNode('form_validation_groups')
                ->addDefaultsIfNotSet()
                ->arrayNode('user')
                    ->addDefaultsIfNotSet()
                    ->prototype('scalar')->end()
                    ->defaultValue(array ('Registration'))
                ->end()
            ->end();
    }

    private function addEmailSection(NodeBuilder $node)
    {
        $node
            ->arrayNode('email')
                ->addDefaultsIfNotSet()
                ->arrayNode('from_email')
                    ->addDefaultsIfNotSet()
                    ->prototype('scalar')->end()
                    ->defaultValue(array('webmaster@example.com' => 'webmaster'))
                ->end()
                ->arrayNode('change_password')
                    ->addDefaultsIfNotSet()
                    ->booleanNode('enabled')->defaultFalse()->end()
                    ->scalarNode('template')->defaultValue('FOSUserBundle:User:confirmationEmail')->end()
                ->end()
                ->arrayNode('confirmation')
                    ->addDefaultsIfNotSet()
                    ->booleanNode('enabled')->defaultFalse()->end()
                    ->scalarNode('template')->defaultValue('FOSUserBundle:User:confirmationEmail')->end()
                ->end()
                ->arrayNode('resetting_password')
                    ->addDefaultsIfNotSet()
                    ->scalarNode('template')->defaultValue('FOSUserBundle:User:resettingPasswordEmail')->end()
                    ->scalarNode('token_ttl')->defaultValue(86400)->end()
                ->end()
            ->end();
    }

    private function addTemplateSection(NodeBuilder $node)
    {
        $node
            ->arrayNode('template')
                ->addDefaultsIfNotSet()
                ->scalarNode('engine')->defaultValue('twig')->end()
                ->scalarNode('theme')->defaultValue('TwigBundle::form.html.twig')->end()
            ->end();
    }
}
