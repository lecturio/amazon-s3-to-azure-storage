<?php
namespace CloudCopy\Origin;

/**
 * List of files from different providers
 *
 * Interface FileSource
 * @package CloudCopy\Origin
 */
interface EntitySource
{
    /**
     * Retrieves entries.
     * @return mixed
     */
    function retrieve();

    /**
     * Removes entries after successful copy of data.
     * @return mixed
     */
    function cleanUp();

    /**
     * Removes entries after certain number of re-tries to process the entry.
     * @return mixed
     */
    function cleanFailed();
}