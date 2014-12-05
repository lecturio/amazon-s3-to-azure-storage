<?php
/**
 * Created by IntelliJ IDEA.
 * User: g
 * Date: 12/5/14
 * Time: 16:15
 */

namespace CloudCopy;


use Aws\S3\S3Client;
use Symfony\Component\DependencyInjection\Container;

class AmazonS3ToAzureStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Container
     */
    private $container;

    function setUp()
    {
        $this->container = $GLOBALS['container'];
    }

    function testWiring()
    {
        /**
         * @var $s3toAzure AmazonS3ToAzureStorage
         */
        $s3toAzure = $this->container->get('amazonS3ToAzureStorage');
        $this->assertTrue($s3toAzure instanceof AmazonS3ToAzureStorage);
        $this->assertTrue($s3toAzure->getS3client() instanceof S3Client);
    }
}
