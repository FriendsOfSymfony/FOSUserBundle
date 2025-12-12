<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use FOS\UserBundle\Controller\RegistrationController;
use FOS\UserBundle\Form\Factory\FormFactory;
use FOS\UserBundle\Form\Type\RegistrationFormType;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('fos_user.registration.form.factory', FormFactory::class)
        ->args([
            service('form.factory'),
            '%fos_user.registration.form.name%',
            '%fos_user.registration.form.type%',
            '%fos_user.registration.form.validation_groups%',
        ]);

    $services->set('fos_user.registration.form.type', RegistrationFormType::class)
        ->args(['%fos_user.model.user.class%'])
        ->tag('form.type', ['alias' => 'fos_user_registration']);

    $services->set('fos_user.registration.controller', RegistrationController::class)
        ->public()
        ->args([
            service('event_dispatcher'),
            service('fos_user.registration.form.factory'),
            service('fos_user.user_manager'),
            service('security.token_storage'),
        ])
        ->call('setContainer', [service(\Psr\Container\ContainerInterface::class)])
        ->tag('container.service_subscriber');

    $services->alias(RegistrationController::class, 'fos_user.registration.controller')
        ->public();
};
