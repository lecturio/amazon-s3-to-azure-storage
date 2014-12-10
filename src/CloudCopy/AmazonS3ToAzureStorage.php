<?php
namespace CloudCopy;


use Aws\S3\S3Client as client;
use CloudCopy\AWS\DownloadResource;
use CloudCopy\AWS\S3Client;
use CloudCopy\Azure\BlobCopy;
use CloudCopy\Azure\StorageClient;
use CloudCopy\Origin\EntitySource;
use CloudCopy\Origin\FileNameBean;
use WindowsAzure\Blob\BlobRestProxy;

class AmazonS3ToAzureStorage
{
    const LINK_AVAILABILITY_TIMEOUT = '5 minutes';

    /**
     * @var client
     */
    private $s3client;

    /**
     * @var BlobRestProxy
     */
    private $storageClient;

    /**
     * @var EntitySource
     */
    private $entitySource;
    /**
     * @var DownloadResource
     */
    private $downloadResource;

    /**
     * @var BlobCopy
     */
    private $copyResource;

    private $config;

    function __construct(
        S3Client $s3Client,
        StorageClient $storageClient,
        EntitySource $entitySource,
        DownloadResource $downloadResource,
        BlobCopy $blobCopy,
        $config
    ) {
        $this->s3client = $s3Client->factory();
        $this->storageClient = $storageClient->factory();
        $this->entitySource = $entitySource;
        $this->downloadResource = $downloadResource;
        $this->copyResource = $blobCopy;
        $this->config = $config;
    }

    function execute()
    {
        foreach ($this->entitySource->retrieve() as $entity) {
            /**
             * @var $entity FileNameBean
             */
            $entity->getEntity();
            $entity->getNode();

            $url = $this->s3client->getObjectUrl($entity->getNode(), $entity->getEntity(),
                self::LINK_AVAILABILITY_TIMEOUT);

            if (preg_match('/./', $entity->getNode())) {
                $url = str_replace($entity->getNode() . '/', '', $url);
                $parsedUrl = parse_url($url);
                $url = sprintf('%s://%s.%s%s?%s', $parsedUrl['scheme'], $entity->getNode(), $parsedUrl['host'],
                    $parsedUrl['path'], $parsedUrl['query']);
            }

            $this->downloadResource->download($url, $entity);
            $this->copyResource->copy($entity);
            //TODO on success write to amazon sqs
        }
    }

    /**
     * @return client
     */
    public function getS3client()
    {
        return $this->s3client;
    }
}
