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
use Twig\Environment;

/**
 * @author Christophe Coevoet <stof@notk.org>
 */
class TwigSwiftMailer extends AbstractMailer
{
    /**
     * @var Environment
     */
    protected $twig;

    /**
     * TwigSwiftMailer constructor.
     */
    public function __construct(\Swift_Mailer $mailer, UrlGeneratorInterface $router, array $parameters, Environment $twig)
    {
        $this->twig = $twig;

        parent::__construct($mailer, $router, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function sendConfirmationEmailMessage(UserInterface $user)
    {
        $template = $this->parameters['template']['confirmation'];
        $url = $this->router->generate('fos_user_registration_confirm', ['token' => $user->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL);

        $context = [
            'user' => $user,
            'confirmationUrl' => $url,
        ];

        $this->sendMessage($this->parameters['from_email']['confirmation'], (string) $user->getEmail(), $template, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function sendResettingEmailMessage(UserInterface $user)
    {
        $template = $this->parameters['template']['resetting'];
        $url = $this->router->generate('fos_user_resetting_reset', ['token' => $user->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL);

        $context = [
            'user' => $user,
            'confirmationUrl' => $url,
        ];

        $this->sendMessage($this->parameters['from_email']['resetting'], (string) $user->getEmail(), $template, $context);
    }

    /**
     * {@inheritDoc}
     */
    protected function sendMessage($fromEmail, $toEmail, $template, $context = [])
    {
        $twigTemplate = $this->twig->load($template);
        $subject = $twigTemplate->renderBlock('subject', $context);
        $textBody = $twigTemplate->renderBlock('body_text', $context);

        $htmlBody = '';

        if ($twigTemplate->hasBlock('body_html', $context)) {
            $htmlBody = $twigTemplate->renderBlock('body_html', $context);
        }

        $message = (new \Swift_Message())
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($toEmail);

        if (!empty($htmlBody)) {
            $message->setBody($htmlBody, 'text/html')
                ->addPart($textBody, 'text/plain');
        } else {
            $message->setBody($textBody);
        }

        $this->mailer->send($message);
    }
}
