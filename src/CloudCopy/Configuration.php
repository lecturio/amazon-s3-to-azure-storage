<?php
namespace CloudCopy;

/**
 * Hold values from config.yml
 *
 * Class Configuration
 * @package Lecturio\CloudCopy
 */
class Configuration
{
    private $config;

    function __construct($config)
    {
        $this->config = $config;
    }

    public function get()
    {
        return $this->config;
    }

    public function getRenameS3Bucket()
    {
        return $this->config['rename']['s3']['bucket'];
    }

}