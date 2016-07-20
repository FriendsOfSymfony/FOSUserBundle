<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Tests\Util;

use FOS\UserBundle\Util\Canonicalizer;

class CanonicalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider canonicalizeProvider
     */
    public function testCanonicalize($source, $expectedResult)
    {
        $canonicalizer = new Canonicalizer();
        $this->assertSame($expectedResult, $canonicalizer->canonicalize($source));
    }

    public function canonicalizeProvider()
    {
        return array(
            array(null, null),
            array('FOO', 'foo'),
            array(chr(171), PHP_VERSION_ID < 50600 ? chr(171) : '?'),
        );
    }
}
