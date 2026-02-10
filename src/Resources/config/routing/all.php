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
    $routes->import('@FOSUserBundle/Resources/config/routing/security.php');

    $routes->import('@FOSUserBundle/Resources/config/profile.php')
        ->prefix('/profile');

    $routes->import('@FOSUserBundle/Resources/config/registration.php')
        ->prefix('/register');

    $routes->import('@FOSUserBundle/Resources/config/resetting.php')
        ->prefix('/resetting');

    $routes->import('@FOSUserBundle/Resources/config/change_password.php')
        ->prefix('/profile');
};
