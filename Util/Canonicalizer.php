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

class Canonicalizer implements CanonicalizerInterface
{
    public function canonicalize($string)
    {
        return mb_convert_case($string, MB_CASE_LOWER, mb_detect_encoding($string));
    }
}
