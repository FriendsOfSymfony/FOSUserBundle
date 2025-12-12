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

/**
 * Registers the additional validators according to the storage.
 *
 * @author Christophe Coevoet <stof@notk.org>
 *
 * @internal
 */
final class ValidationPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('fos_user.storage')) {
            return;
        }

        $storage = $container->getParameter('fos_user.storage');
        \assert(\is_string($storage));

        if ('custom' === $storage) {
            return;
        }

        $validationFile = __DIR__.'/../../Resources/config/storage-validation/'.$storage.'.yaml';

        $container->getDefinition('validator.builder')
            ->addMethodCall('addYamlMapping', [$validationFile]);
    }
}
