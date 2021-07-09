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

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class Mailer extends AbstractMailer
{
    /**
     * @var TemplateInterface
     */
    protected $template;

    /**
     * Mailer constructor.
     */
    public function __construct(\Swift_Mailer $mailer, UrlGeneratorInterface $router, array $parameters, TemplateInterface $template)
    {
        $this->template = $template;

        parent::__construct($mailer, $router, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function sendConfirmationEmailMessage(UserInterface $user)
    {
        $template = $this->parameters['confirmation.template'];
        $url = $this->router->generate('fos_user_registration_confirm', ['token' => $user->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL);
        $rendered = $this->template->render($template, [
            'user' => $user,
            'confirmationUrl' => $url,
        ]);
        $this->sendMessage($this->parameters['from_email']['confirmation'], (string) $user->getEmail(), $rendered);
    }

    /**
     * {@inheritdoc}
     */
    public function sendResettingEmailMessage(UserInterface $user)
    {
        $template = $this->parameters['resetting.template'];
        $url = $this->router->generate('fos_user_resetting_reset', ['token' => $user->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL);
        $rendered = $this->template->render($template, [
            'user' => $user,
            'confirmationUrl' => $url,
        ]);
        $this->sendMessage($this->parameters['from_email']['resetting'], (string) $user->getEmail(), $rendered);
    }

    /**
     * {@inheritDoc}
     */
    protected function sendMessage($fromEmail, $toEmail, $template, $context = [])
    {
        // Render the email, use the first line as the subject, and the rest as the body
        $renderedLines = explode("\n", trim($template));
        $subject = array_shift($renderedLines);
        $body = implode("\n", $renderedLines);

        $message = (new \Swift_Message())
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($toEmail)
            ->setBody($body);

        $this->mailer->send($message);
    }
}
