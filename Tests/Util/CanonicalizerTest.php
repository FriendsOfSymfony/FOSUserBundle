<?php

namespace FOS\UserBundle\Tests\Util;

use FOS\UserBundle\Util\Canonicalizer;

/**
 * Description of CanonicalizerTest
 *
 * @author Bryan J. Agee <bryan@pamiris.com>
 */
class CanonicalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var Canonicalizer
     */
    private $canonicalizer;
    
    public function setUp()
    {
        $this->canonicalizer = new Canonicalizer;
    }
    
    public static function canonicalizerTestProvider()
    {
        return array(
            array('aFunkyMiXedCaseString', 'afunkymixedcasestring'),
            array('anEmail.Address@something.NET', 'anemail.address@something.net'),
        );
    }

    /**
     * 
     * @dataProvider canonicalizerTestProvider
     */
    public function testCanonicalizer($string, $canned)
    {
        $result = $this->canonicalizer->canonicalize($string);
        $this->assertEquals($canned, $result);
    }
}
