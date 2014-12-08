<?php
namespace CloudCopy\Origin;

/**
 * List of files from different providers
 *
 * Interface FileSource
 * @package CloudCopy\Origin
 */
interface FileSource
{
    function retrieveFiles();
}