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

use FOS\UserBundle\EventListener\AuthenticationListener;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('fos_user.listener.authentication', AuthenticationListener::class)
        ->args([
            service('fos_user.security.login_manager'),
            '%fos_user.firewall_name%',
        ])
        ->tag('kernel.event_subscriber');
};
