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
    $routes->add('fos_user_security_login', '/login')
        ->methods(['GET', 'POST'])
        ->controller('fos_user.security.controller::loginAction');

    $routes->add('fos_user_security_check', '/login_check')
        ->methods(['POST'])
        ->controller('fos_user.security.controller::checkAction');

    $routes->add('fos_user_security_logout', '/logout')
        ->methods(['GET', 'POST'])
        ->controller('fos_user.security.controller::logoutAction');
};
