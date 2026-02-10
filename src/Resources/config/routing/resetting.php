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
    $routes->add('fos_user_resetting_request', '/request')
        ->methods(['GET'])
        ->controller('fos_user.resetting.controller::requestAction');

    $routes->add('fos_user_resetting_send_email', '/send-email')
        ->methods(['POST'])
        ->controller('fos_user.resetting.controller::sendEmailAction');

    $routes->add('fos_user_resetting_check_email', '/check-email')
        ->methods(['GET'])
        ->controller('fos_user.resetting.controller::checkEmailAction');

    $routes->add('fos_user_resetting_reset', '/reset/{token}')
        ->methods(['GET', 'POST'])
        ->controller('fos_user.resetting.controller::resetAction');
};
