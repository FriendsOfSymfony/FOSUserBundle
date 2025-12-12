<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use FOS\UserBundle\EventListener\FlashListener;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('fos_user.listener.flash', FlashListener::class)
        ->args([
            service('request_stack'),
            service('translator'),
        ])
        ->tag('kernel.event_subscriber');
};
