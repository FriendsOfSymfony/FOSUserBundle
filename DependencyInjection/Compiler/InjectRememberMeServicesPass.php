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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Registers the additional validators according to the storage
 *
 * @author Vasily Khayrulin <sirianru@gmail.com>
 */
class InjectRememberMeServicesPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {

        $rememberMeServices = array();
        foreach ($container->getDefinitions() as $id => $definition) {
            if (0 !== strpos($id, 'security.authentication.rememberme.services.')) {
                continue;
            }
            if ($definition->isAbstract()) {
                continue;
            }
            $firewallName = $definition->getArgument(2);
            $rememberMeServices[$firewallName] = new Reference($id);
        }

        $loginManager = $container->getDefinition('fos_user.security.login_manager');
        $loginManager->replaceArgument(4, $rememberMeServices);
    }
}
