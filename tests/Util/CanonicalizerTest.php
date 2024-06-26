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
use PHPUnit\Framework\TestCase;

class CanonicalizerTest extends TestCase
{
    /**
     * @dataProvider canonicalizeProvider
     */
    public function testCanonicalize(?string $source, ?string $expectedResult)
    {
        $canonicalizer = new Canonicalizer();
        $this->assertSame($expectedResult, $canonicalizer->canonicalize($source));
    }

    /**
     * @return iterable<array{string|null, string|null}>
     */
    public function canonicalizeProvider(): iterable
    {
        return [
            [null, null],
            ['FOO', 'foo'],
            [chr(171), '?'],
        ];
    }
}
