<?php
namespace CloudCopy;

use Symfony\Component\DependencyInjection\Container;

class BootStrapTest extends \PHPUnit_Framework_TestCase
{
    function testBootstrappingAmazonS3ToAzureStorage()
    {
        $this->assertNotNull($GLOBALS['container']);
        $this->assertTrue($GLOBALS['container'] instanceof Container);
    }
}