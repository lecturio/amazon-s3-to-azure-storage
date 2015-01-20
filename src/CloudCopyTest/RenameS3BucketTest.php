<?php
namespace CloudCopy;


class RenameS3BucketTest extends \PHPUnit_Framework_TestCase {

    public function testWiring() {
        $container = $GLOBALS['container'];

        $renameS3Bucket = $container->get('renameS3Bucket');

        $this->assertTrue($renameS3Bucket instanceof RenameS3Bucket);
    }

}