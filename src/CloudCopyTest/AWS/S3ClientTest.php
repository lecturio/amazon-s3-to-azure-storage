<?php
namespace CloudCopy\AWS;


use Aws\Common\Credentials\Credentials;
use Symfony\Component\DependencyInjection\Container;

class S3ClientTest extends \PHPUnit_Framework_TestCase
{
    private $client;
    /**
     * @var Container
     */
    private $container;

    function setUp()
    {
        $this->container = $GLOBALS['container'];
        $this->client = $this->container->get('s3client');
    }

    function testWiring()
    {
        $this->assertNotNull($this->client);
    }

    function testCreate()
    {
        $this->assertNotNull($this->client->factory());
    }

    function testCredentialsWiring()
    {
        /**
         * @var $credentials Credentials
         */
        $credentials = $this->client->factory()->getCredentials();
        $this->assertNotNull($credentials);
        $this->assertNotEmpty($credentials->getSecretKey());
        $this->assertNotEmpty($credentials->getAccessKeyId());
    }
}
