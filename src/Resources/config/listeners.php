<?php

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
