<?php
namespace CloudCopy\Azure;

use Symfony\Component\DependencyInjection\Container;
use WindowsAzure\Blob\BlobRestProxy;

class StorageClientTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var $container Container
     */
    private $container;

    function setUp()
    {
        $this->container = $GLOBALS['container'];
    }

    function testWiring()
    {
        /**
         * @var $storageClient StorageClient
         */
        $storageClient = $this->container->get('storageClient');
        $this->assertTrue($storageClient instanceof StorageClient);
        $this->assertTrue(is_array($storageClient->getConfig()));
    }

    function testWirinbgBlobProxy()
    {
        /**
         * @var $storageClient StorageClient
         */
        $storageClient = $this->container->get('storageClient');
        $this->assertTrue($storageClient->factory() instanceof BlobRestProxy);
    }

}
