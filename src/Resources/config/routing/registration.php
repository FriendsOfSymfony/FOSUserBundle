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
    $routes->add('fos_user_registration_register', '/')
        ->methods(['GET', 'POST'])
        ->controller('fos_user.registration.controller::registerAction');

    $routes->add('fos_user_registration_check_email', '/check-email')
        ->methods(['GET'])
        ->controller('fos_user.registration.controller::checkEmailAction');

    $routes->add('fos_user_registration_confirm', '/confirm/{token}')
        ->methods(['GET'])
        ->controller('fos_user.registration.controller::confirmAction');

    $routes->add('fos_user_registration_confirmed', '/confirmed')
        ->methods(['GET'])
        ->controller('fos_user.registration.controller::confirmedAction');
};
