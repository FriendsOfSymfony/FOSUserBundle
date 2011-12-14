<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Security\Encoder;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\UserInterface as SecurityUserInterface;

/**
 * This factory assumes MessageDigestPasswordEncoder's constructor.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 * @author Jeremy Mikola <jmikola@gmail.com>
 */
class EncoderFactory implements EncoderFactoryInterface
{
    protected $encoders;
    protected $encoderClass;
    protected $encodeHashAsBase64;
    protected $iterations;
    protected $genericFactory;

    /**
     * Constructor.
     *
     * @param string $encoderClass Encoder class
     * @param Boolean $encodeHashAsBase64
     * @param integer $iterations
     * @param EncoderFactoryInterface $genericFactory
     */
    public function __construct($encoderClass, $encodeHashAsBase64, $iterations, EncoderFactoryInterface $genericFactory)
    {
        $this->encoders = array();
        $this->encoderClass = $encoderClass;
        $this->encodeHashAsBase64 = $encodeHashAsBase64;
        $this->iterations = $iterations;
        $this->genericFactory = $genericFactory;
    }

    /**
     * @see Symfony\Component\Security\Core\Encoder\EncoderFactory::getEncoder()
     */
    public function getEncoder(SecurityUserInterface $user)
    {
        if (!$user instanceof UserInterface) {
            return $this->genericFactory->getEncoder($user);
        }

        if (isset($this->encoders[$algorithm = $user->getAlgorithm()])) {
            return $this->encoders[$algorithm];
        }

        return $this->encoders[$algorithm] = $this->createEncoder($algorithm);
    }

    /**
     * Creates an encoder for the given algorithm.
     *
     * @param string $algorithm
     *
     * @return PasswordEncoderInterface
     */
    protected function createEncoder($algorithm)
    {
        $class = $this->encoderClass;

        return new $class(
            $algorithm,
            $this->encodeHashAsBase64,
            $this->iterations
        );
    }
}
