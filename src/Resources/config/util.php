<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Util\CanonicalFieldsUpdater;
use FOS\UserBundle\Util\Canonicalizer;
use FOS\UserBundle\Util\HashingPasswordUpdater;
use FOS\UserBundle\Util\PasswordUpdaterInterface;
use FOS\UserBundle\Util\TokenGenerator;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use FOS\UserBundle\Util\UserManipulator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('fos_user.util.canonicalizer.default', Canonicalizer::class)
        ->private();

    $services->set('fos_user.util.user_manipulator', UserManipulator::class)
        ->args([
            service('fos_user.user_manager'),
            service('event_dispatcher'),
            service('request_stack'),
        ]);

    $services->set('fos_user.util.token_generator.default', TokenGenerator::class)
        ->private();

    $services->alias(TokenGeneratorInterface::class, 'fos_user.util.token_generator')
        ->private();

    $services->set('fos_user.util.password_updater', HashingPasswordUpdater::class)
        ->private()
        ->args([service('security.password_hasher_factory')]);

    $services->alias(PasswordUpdaterInterface::class, 'fos_user.util.password_updater')
        ->private();

    $services->set('fos_user.util.canonical_fields_updater', CanonicalFieldsUpdater::class)
        ->private()
        ->args([
            service('fos_user.util.username_canonicalizer'),
            service('fos_user.util.email_canonicalizer'),
        ]);

    $services->alias(CanonicalFieldsUpdater::class, 'fos_user.util.canonical_fields_updater')
        ->private();

    $services->alias(UserManagerInterface::class, 'fos_user.user_manager')
        ->private();
};
