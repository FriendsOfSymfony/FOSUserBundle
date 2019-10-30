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
use Symfony\Flex\Recipe;

/**
 * Checks to see if the mailer service exists.
 *
 * @author Ryan Weaver <ryan@knpuniversity.com>
 *
 * @deprecated since 2.1, "mailer" service should be optional
 */
class CheckForMailerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        // if the mailer isn't needed, then no error needed
        if (!$container->has('fos_user.mailer')) {
            return;
        }

        // the mailer exists, so all is good
        if ($container->has('mailer')) {
            return;
        }

        if ($container->findDefinition('fos_user.mailer')->hasTag('fos_user.requires_swift')) {
            $message = 'A feature you activated in FOSUserBundle requires the "mailer" service to be available.';

            if (class_exists(Recipe::class)) {
                $message .= ' Run "composer require swiftmailer-bundle" to install SwiftMailer or configure a different mailer in "config/packages/fos_user.yaml".';
            }

            throw new \LogicException($message);
        }
    }
}
