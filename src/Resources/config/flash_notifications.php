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
