<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Util;

use Psr\Log\LoggerInterface;

class TokenGenerator implements TokenGeneratorInterface
{
    private $logger;
    private $useOpenSsl = true;

    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;

        // determine whether to use OpenSSL
        if (defined('PHP_WINDOWS_VERSION_BUILD') && version_compare(PHP_VERSION, '5.3.4', '<')) {
            return $this->useOpenSsl = false;
        }

        if (function_exists('openssl_random_pseudo_bytes')) {
            return;
        }
        
        if (null !== $this->logger) {
            $this->logger->notice('It is recommended that you enable the "openssl" extension for random number generation.');
        }
        $this->useOpenSsl = false;
    }
    
    /**
     * Allows manaul disabling of OpenSSL (primarily for testing)
     */
    public function disableOpenSsl()
    {
        $this->useOpenSsl = false;
    }

    public function generateToken()
    {
        return rtrim(strtr(base64_encode($this->getRandomNumber()), '+/', '-_'), '=');
    }

    private function getRandomNumber()
    {
        $nbBytes = 32;

        // try OpenSSL
        if ($this->useOpenSsl) {
            $bytes = openssl_random_pseudo_bytes($nbBytes, $strong);

            if (false !== $bytes && true === $strong) {
                return $bytes;
            }

            if (null !== $this->logger) {
                $this->logger->info('OpenSSL did not produce a secure random number.');
            }
        }

        return hash('sha256', uniqid(mt_rand(), true), true);
    }
}
