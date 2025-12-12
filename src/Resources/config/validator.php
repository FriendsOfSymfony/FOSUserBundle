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

use FOS\UserBundle\Validator\Initializer;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('fos_user.validator.initializer', Initializer::class)
        ->private()
        ->args([service('fos_user.util.canonical_fields_updater')])
        ->tag('validator.initializer');
};
