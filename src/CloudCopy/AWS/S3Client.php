<?php
namespace CloudCopy\AWS;

use Aws\S3\S3Client as client;

/**
 * Adapter for S3 client.
 * @package CloudCopy\AWS
 */
class S3Client
{
    /**
     * @var client
     */
    private $s3Client;

    function __construct($config = array())
    {
        $this->s3Client = client::factory(array(
            'key' => $config['aws']['access.key'],
            'secret' => $config['aws']['secret.key']
        ));

        //$this->s3Client->setBaseUrl('.s3.amazonaws.com');
    }

    /**
     * @return client
     */
    function factory()
    {
        return $this->s3Client;
    }

}