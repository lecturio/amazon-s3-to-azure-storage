<?php

namespace CloudCopy\Origin;

use Symfony\Component\DependencyInjection\Container;

class LocalStorageTest extends \PHPUnit_Framework_TestCase
{

    function testRetrieveFiles()
    {
        $localStorage = new LocalStorage(array('local.sourcelist' => __DIR__));
        $entities = $localStorage->retrieve();

        /**
         * @var $filename FileNameBean
         */
        $filename = $entities[0];
        $this->assertEquals('video_640x360_0,6Mbit.mp4', $filename->getEntity());
        $this->assertEquals('old.bucketname', $filename->getNode());

        $filename = $entities[1];
        $this->assertEquals('video_0,25Mbit_1.mp4', $filename->getEntity());
        $this->assertEquals('new-bucketname', $filename->getNode());
    }

    function testWiring()
    {
        /**
         * @var $container Container
         */
        $container = $GLOBALS['container'];

        /**
         * @var $localStorage LocalStorage
         */
        $localStorage = $container->get('localStorage');
        $this->asserttrue($localStorage instanceof LocalStorage);
        $this->asserttrue(is_array($localStorage->getConfig()));
    }

}
