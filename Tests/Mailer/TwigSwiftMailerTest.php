<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Tests\Mailer;

use FOS\UserBundle\Mailer\TwigSwiftMailer;
use PHPUnit\Framework\TestCase;
use Swift_Mailer;
use Swift_Transport_NullTransport;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class TwigSwiftMailerTest extends TestCase
{
    /**
     * @dataProvider goodEmailProvider
     */
    public function testSendConfirmationEmailMessageWithGoodEmails($emailAddress)
    {
        $mailer = $this->getTwigSwiftMailer();
        $mailer->sendConfirmationEmailMessage($this->getUser($emailAddress));

        $this->assertTrue(true);
    }

    /**
     * @dataProvider badEmailProvider
     * @expectedException \Swift_RfcComplianceException
     */
    public function testSendConfirmationEmailMessageWithBadEmails($emailAddress)
    {
        $mailer = $this->getTwigSwiftMailer();
        $mailer->sendConfirmationEmailMessage($this->getUser($emailAddress));
    }

    /**
     * @dataProvider goodEmailProvider
     */
    public function testSendResettingEmailMessageWithGoodEmails($emailAddress)
    {
        $mailer = $this->getTwigSwiftMailer();
        $mailer->sendResettingEmailMessage($this->getUser($emailAddress));

        $this->assertTrue(true);
    }

    /**
     * @dataProvider badEmailProvider
     * @expectedException \Swift_RfcComplianceException
     */
    public function testSendResettingEmailMessageWithBadEmails($emailAddress)
    {
        $mailer = $this->getTwigSwiftMailer();
        $mailer->sendResettingEmailMessage($this->getUser($emailAddress));
    }

    public function goodEmailProvider()
    {
        return [
            ['foo@example.com'],
            ['foo@example.co.uk'],
            [$this->getEmailAddressValueObject('foo@example.com')],
            [$this->getEmailAddressValueObject('foo@example.co.uk')],
        ];
    }

    public function badEmailProvider()
    {
        return [
            ['foo'],
            [$this->getEmailAddressValueObject('foo')],
        ];
    }

    private function getTwigSwiftMailer()
    {
        return new TwigSwiftMailer(
            new Swift_Mailer(
                new Swift_Transport_NullTransport(
                    $this->getMockBuilder('Swift_Events_EventDispatcher')->getMock()
                )
            ),
            $this->getMockBuilder('Symfony\Component\Routing\Generator\UrlGeneratorInterface')->getMock(),
            $this->getTwigEnvironment(),
            [
                'template' => [
                    'confirmation' => 'foo',
                    'resetting' => 'foo',
                ],
                'from_email' => [
                    'confirmation' => 'foo@example.com',
                    'resetting' => 'foo@example.com',
                ],
            ]
        );
    }

    private function getTwigEnvironment()
    {
        return new Environment(new ArrayLoader(['foo' => <<<'TWIG'
{% block subject 'foo' %}

{% block body_text %}Test{% endblock %}

TWIG
        ]));
    }

    private function getUser($emailAddress)
    {
        $user = $this->getMockBuilder('FOS\UserBundle\Model\UserInterface')->getMock();
        $user->method('getEmail')
            ->willReturn($emailAddress)
        ;

        return $user;
    }

    private function getEmailAddressValueObject($emailAddressAsString)
    {
        $emailAddress = $this->getMockBuilder('EmailAddress')
           ->setMethods(['__toString'])
           ->getMock();

        $emailAddress->method('__toString')
            ->willReturn($emailAddressAsString)
        ;

        return $emailAddress;
    }
}
