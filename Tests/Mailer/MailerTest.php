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

use FOS\UserBundle\Mailer\Mailer;
use PHPUnit\Framework\TestCase;
use Swift_Mailer;
use Swift_Transport_NullTransport;

class MailerTest extends TestCase
{
    /**
     * @dataProvider goodEmailProvider
     */
    public function testSendConfirmationEmailMessageWithGoodEmails($emailAddress)
    {
        $mailer = $this->getMailer();
        $mailer->sendConfirmationEmailMessage($this->getUser($emailAddress));

        $this->assertTrue(true);
    }

    /**
     * @dataProvider badEmailProvider
     * @expectedException \Swift_RfcComplianceException
     */
    public function testSendConfirmationEmailMessageWithBadEmails($emailAddress)
    {
        $mailer = $this->getMailer();
        $mailer->sendConfirmationEmailMessage($this->getUser($emailAddress));
    }

    /**
     * @dataProvider goodEmailProvider
     */
    public function testSendResettingEmailMessageWithGoodEmails($emailAddress)
    {
        $mailer = $this->getMailer();
        $mailer->sendResettingEmailMessage($this->getUser($emailAddress));

        $this->assertTrue(true);
    }

    /**
     * @dataProvider badEmailProvider
     * @expectedException \Swift_RfcComplianceException
     */
    public function testSendResettingEmailMessageWithBadEmails($emailAddress)
    {
        $mailer = $this->getMailer();
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

    private function getMailer()
    {
        return new Mailer(
            new Swift_Mailer(
                new Swift_Transport_NullTransport(
                    $this->getMockBuilder('\Swift_Events_EventDispatcher')->getMock()
                )
            ),
            $this->getMockBuilder('Symfony\Component\Routing\Generator\UrlGeneratorInterface')->getMock(),
            $this->getTemplating(),
            [
                'confirmation.template' => 'foo',
                'resetting.template' => 'foo',
                'from_email' => [
                    'confirmation' => 'foo@example.com',
                    'resetting' => 'foo@example.com',
                ],
            ]
        );
    }

    private function getTemplating()
    {
        $templating = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        return $templating;
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
