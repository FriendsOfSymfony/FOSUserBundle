<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use FOS\UserBundle\Controller\ResettingController;
use FOS\UserBundle\EventListener\ResettingListener;
use FOS\UserBundle\Form\Factory\FormFactory;
use FOS\UserBundle\Form\Type\ResettingFormType;
use Psr\Container\ContainerInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('fos_user.resetting.form.factory', FormFactory::class)
        ->args([
            service('form.factory'),
            '%fos_user.resetting.form.name%',
            '%fos_user.resetting.form.type%',
            '%fos_user.resetting.form.validation_groups%',
        ]);

    $services->set('fos_user.resetting.form.type', ResettingFormType::class)
        ->args(['%fos_user.model.user.class%'])
        ->tag('form.type', ['alias' => 'fos_user_resetting']);

    $services->set('fos_user.listener.resetting', ResettingListener::class)
        ->args([
            service('router'),
            '%fos_user.resetting.token_ttl%',
        ])
        ->tag('kernel.event_subscriber');

    $services->set('fos_user.resetting.controller', ResettingController::class)
        ->public()
        ->args([
            service('event_dispatcher'),
            service('fos_user.resetting.form.factory'),
            service('fos_user.user_manager'),
            service('fos_user.util.token_generator'),
            service('fos_user.mailer'),
            '%fos_user.resetting.retry_ttl%',
        ])
        ->call('setContainer', [service(ContainerInterface::class)])
        ->tag('container.service_subscriber');

    $services->alias(ResettingController::class, 'fos_user.resetting.controller')
        ->public();
};
