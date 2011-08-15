<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\DependencyInjection\Security\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;

/**
 * OAuth Factory for setting up oauth related services hooking into
 * the security component
 */
class OAuthFactory implements SecurityFactoryInterface
{

    public function create(ContainerBuilder $container, $id, $config, $userProvider,  $defaultEntryPoint)
    {

        $providerId = 'security.authentication.provider.oauth.'.$id;
        $container ->setDefinition($providerId, new DefinitionDecorator('fos_user.security.authentication.provider.oauth'))
            ->replaceArgument(0, new Reference($userProvider));

        $listenerId = 'security.authentication.listener.oauth.'.$id;
        $container->setDefinition($listenerId, new DefinitionDecorator('fos_user.security.authentication.listener.oauth'));

        return array($providerId, $listenerId, $defaultEntryPoint);
    }

    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'oauth';
    }

    public function addConfiguration(NodeDefinition $node)
    {}
}