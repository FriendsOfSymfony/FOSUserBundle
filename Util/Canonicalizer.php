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

/**
 * The Canonicalizer centralizes the task of normalizing strings for searching
 * and indexing--especailly in case-sensitive datastores.
 */
class Canonicalizer implements CanonicalizerInterface
{
    /**
     * 
     * @param string $string
     * @return string
     */
    public function canonicalize($string)
    {
        return null === $string ? null 
                : mb_convert_case($string, MB_CASE_LOWER, mb_detect_encoding($string));
    }
}
