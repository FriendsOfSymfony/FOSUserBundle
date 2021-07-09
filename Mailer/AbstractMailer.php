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
 * Class AbstractMailer.
 *
 * @author Nikolay Nikolaev <evrinoma@gmail.com>
 */
abstract class AbstractMailer implements MailerInterface
{
    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var UrlGeneratorInterface
     */
    protected $router;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * AbstractMailer constructor.
     */
    public function __construct(\Swift_Mailer $mailer, UrlGeneratorInterface $router, array $parameters)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->parameters = $parameters;
    }

    /**
     * @param array|string $fromEmail
     * @param array|string $toEmail
     * @param string       $template
     * @param array        $context
     */
    abstract protected function sendMessage($fromEmail, $toEmail, $template, $context = []);
}
