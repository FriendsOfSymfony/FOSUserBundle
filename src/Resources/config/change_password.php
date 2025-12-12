<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use FOS\UserBundle\Controller\ChangePasswordController;
use FOS\UserBundle\Form\Factory\FormFactory;
use FOS\UserBundle\Form\Type\ChangePasswordFormType;
use Psr\Container\ContainerInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('fos_user.change_password.form.factory', FormFactory::class)
        ->args([
            service('form.factory'),
            '%fos_user.change_password.form.name%',
            '%fos_user.change_password.form.type%',
            '%fos_user.change_password.form.validation_groups%',
        ]);

    $services->set('fos_user.change_password.form.type', ChangePasswordFormType::class)
        ->args(['%fos_user.model.user.class%'])
        ->tag('form.type', ['alias' => 'fos_user_change_password']);

    $services->set('fos_user.change_password.controller', ChangePasswordController::class)
        ->public()
        ->args([
            service('event_dispatcher'),
            service('fos_user.change_password.form.factory'),
            service('fos_user.user_manager'),
        ])
        ->call('setContainer', [service(ContainerInterface::class)])
        ->tag('container.service_subscriber');

    $services->alias(ChangePasswordController::class, 'fos_user.change_password.controller')
        ->public();
};
