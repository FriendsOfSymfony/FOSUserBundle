<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Doctrine\Persistence\ObjectManager;
use FOS\UserBundle\Doctrine\UserListener;
use FOS\UserBundle\Doctrine\UserManager;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('fos_user.user_manager.default', UserManager::class)
        ->private()
        ->args([
            service('fos_user.util.password_updater'),
            service('fos_user.util.canonical_fields_updater'),
            service('fos_user.object_manager'),
            '%fos_user.model.user.class%',
        ]);

    $services->set('fos_user.object_manager', ObjectManager::class)
        ->private()
        ->args(['%fos_user.model_manager_name%']);

    $services->set('fos_user.user_listener', UserListener::class)
        ->private()
        ->args([
            service('fos_user.util.password_updater'),
            service('fos_user.util.canonical_fields_updater'),
        ]);
};
