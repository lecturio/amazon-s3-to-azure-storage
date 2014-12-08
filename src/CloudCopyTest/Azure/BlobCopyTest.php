<?php

namespace CloudCopy\Azure;

use Symfony\Component\DependencyInjection\Container as di;
use WindowsAzure\Blob\BlobRestProxy;

class BlobCopyTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var di
     */
    private $container;

    function setUp()
    {
        $this->container = $GLOBALS['container'];
    }

    function testWiring()
    {
        /**
         * @var $blobCopy BlobCopy
         */
        $blobCopy = $this->container->get('blobCopy');
        $this->assertTrue($blobCopy instanceof BlobCopy);
        $this->assertTrue($blobCopy->getClient() instanceOf BlobRestProxy);
        $this->assertTrue(is_array($blobCopy->getConfig()));
    }
}
