<?php

/**
 * (c) Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\FOS\UserBundle\Security\Encoder;

use Bundle\FOS\UserBundle\Model\User;

/**
 * This factory assumes MessageDigestPasswordEncoder's constructor.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class EncoderFactory implements EncoderFactoryInterface
{
    protected static $encoders = array();
    protected $encoderClass;
    protected $encodeHashAsBase64;
    protected $iterations;

    public function __construct($class, $encodeHashAsBase64, $iterations)
    {
        $this->encoderClass = $class;
        $this->encodeHashAsBase64 = $encodeHashAsBase64;
        $this->iterations = $iterations;
    }

    public function getEncoder(User $user)
    {
        if (!isset(self::$encoders[$algorithm = $user->getAlgorithm()])) {
            $class = $this->encoderClass;

            self::$encoders[$algorithm] = new $class($algorithm, $this->encodeHashAsBase64, $this->iterations);
        }

        return self::$encoders[$algorithm];
    }
}