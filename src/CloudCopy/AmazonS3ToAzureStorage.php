<?php
namespace CloudCopy;


use Aws\S3\S3Client as client;
use CloudCopy\AWS\S3Client;

class AmazonS3ToAzureStorage
{
    /**
     * @var client
     */
    private $s3client;

    private $config;

    function __construct(S3Client $s3Client, $config)
    {
        $this->s3client = $s3Client->factory();
        $this->config = $config;
    }

    function execute()
    {
        $bucket = $this->config['aws']['buckets'];
        //TODO copy file from amazon to local storage
        //FIXME bucket names with . cause amazon to server old urls
        $url = $this->s3client->getObjectUrl($bucket,
            'file.mp4', '5 minutes');

        return "Process copy from Amazon to Azure";
    }

    /**
     * @return client
     */
    public function getS3client()
    {
        return $this->s3client;
    }
}
