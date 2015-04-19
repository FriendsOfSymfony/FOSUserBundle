<?php

namespace FOS\UserBundle\Tests\Util;

use FOS\UserBundle\Util\TokenGenerator;
use Psr\Log\NullLogger;

/**
 * Description of TokenGeneratorTest
 *
 * @author Bryan J. Agee <bryan@pamiris.com>
 */
class TokenGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    
    /**
     *
     * @var TokenGenerator
     */
    private $tokenGenerator;
    
    public function setUp ()
    {
        $this->logger = new NullLogger;
        $this->tokenGenerator = new TokenGenerator($this->logger);
    }
    
    public function testConstruct()
    {
        $this->assertInstanceOf('FOS\UserBundle\Util\TokenGenerator', $this->tokenGenerator);
    }
    
    public function testGetToken()
    {
        // We want to run the generator a few times to make sure that we don't
        // get duplicates
        $iterations = 200;
        $tokens = array();
        for($i = 0; $i < $iterations; $i++) {
            $token = $this->tokenGenerator->generateToken();
            $this->assertGreaterThan(32, strlen($token));
            $tokens[] = $token;
        }
        
        $uniqueTokens = array_unique($tokens);
        $this->assertEquals(sizeof($uniqueTokens), $iterations);
    }
    
    public function testGetTokenWithoutLogger()
    {
        $this->tokenGenerator = new TokenGenerator();
        $this->testGetToken();
    }
    
    public function testGetTokenWithoutOpenSSL()
    {
        $this->tokenGenerator->disableOpenSsl();
        $this->testGetToken();
    }
}
