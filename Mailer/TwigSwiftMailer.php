<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Mailer;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author Christophe Coevoet <stof@notk.org>
 *
 * @deprecated TwigSwiftMailer class is deprecated since 2.1, use TwigMailer instead.
 */
class TwigSwiftMailer extends TwigMailer
{
    /**
     * TwigSwiftMailer constructor.
     *
     * @param \Swift_Mailer         $mailer
     * @param UrlGeneratorInterface $router
     * @param \Twig_Environment     $twig
     * @param array                 $parameters
     */
    public function __construct(\Swift_Mailer $mailer, UrlGeneratorInterface $router, \Twig_Environment $twig, array $parameters)
    {
        @trigger_error(sprintf(
            '%s is deprecated, use %s instead.', __CLASS__, TwigMailer::class
        ), E_USER_DEPRECATED);

        parent::__construct($mailer, $router, $twig, $parameters);
    }
}
