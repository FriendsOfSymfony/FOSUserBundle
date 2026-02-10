<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Loader\Configurator;

return static function (RoutingConfigurator $routes): void {
    $routes->add('fos_user_profile_show', '/')
        ->methods(['GET'])
        ->controller('fos_user.profile.controller::showAction');

    $routes->add('fos_user_profile_edit', '/edit')
        ->methods(['GET', 'POST'])
        ->controller('fos_user.profile.controller::editAction');
};
