<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use FOS\UserBundle\Controller\ProfileController;
use FOS\UserBundle\Form\Factory\FormFactory;
use FOS\UserBundle\Form\Type\ProfileFormType;
use Psr\Container\ContainerInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('fos_user.profile.form.factory', FormFactory::class)
        ->args([
            service('form.factory'),
            '%fos_user.profile.form.name%',
            '%fos_user.profile.form.type%',
            '%fos_user.profile.form.validation_groups%',
        ]);

    $services->set('fos_user.profile.form.type', ProfileFormType::class)
        ->args(['%fos_user.model.user.class%'])
        ->tag('form.type', ['alias' => 'fos_user_profile']);

    $services->set('fos_user.profile.controller', ProfileController::class)
        ->public()
        ->args([
            service('event_dispatcher'),
            service('fos_user.profile.form.factory'),
            service('fos_user.user_manager'),
        ])
        ->call('setContainer', [service(ContainerInterface::class)])
        ->tag('container.service_subscriber');

    $services->alias(ProfileController::class, 'fos_user.profile.controller')
        ->public();
};
