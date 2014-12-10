<?php
namespace CloudCopy\Origin\Resource;


use CloudCopy\Origin\FileNameBean;

class NameParserTest extends \PHPUnit_Framework_TestCase
{

    function testWithSingleSlashName()
    {
        $parser = new NameParser();
        /**
         * @var $fileName FileNameBean
         */

        $fileName = $parser->retrieveEntityBean('bucket.name/file.name');
        $this->assertEquals('bucket.name', $fileName->getNode());
        $this->assertEquals('file.name', $fileName->getEntity());

    }

    function testWithMultiSlashName()
    {
        $parser = new NameParser();
        /**
         * @var $fileName FileNameBean
         */

        $fileName = $parser->retrieveEntityBean('bucket.name/local.path/file.name');
        $this->assertEquals('bucket.name', $fileName->getNode());
        $this->assertEquals('local.path/file.name', $fileName->getEntity());
    }
}
