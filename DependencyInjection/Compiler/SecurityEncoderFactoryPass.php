<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Alias;

/**
 * Overwrites the existing encoder factory and injects the old one in the FOSUserBundle implementation
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class SecurityEncoderFactoryPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->hasAlias('security.encoder_factory')) {
            // security.encoder_factory is an alias.
            // Register a private alias for this service to inject it as the parent
            $container->setAlias('fos_user.encoder_factory.parent', new Alias((string) $container->getAlias('security.encoder_factory'), false));
        } else {
            // security.encoder_factory is a definition.
            // Register it again as a private service to inject it as the parent
            $definition = $container->getDefinition('security.encoder_factory');
            $definition->setPublic(false);
            $container->setDefinition('fos_user.encoder_factory.parent', $definition);
        }

        $container->setAlias('security.encoder_factory', 'fos_user.encoder_factory');
    }
}
