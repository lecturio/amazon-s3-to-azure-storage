<?php
namespace CloudCopy\Azure;


use WindowsAzure\Blob\BlobRestProxy;
use WindowsAzure\Common\ServicesBuilder;

/**
 * Create blob storage client.
 *
 * Class StorageClient
 * @package CloudCopy\Azure
 */
class StorageClient
{

    /**
     * @var BlobRestProxy
     */
    private $blobRestProxy;

    private $config;

    function __construct($config = array())
    {
        $connectionString = sprintf("DefaultEndpointsProtocol=http;AccountName=%s;AccountKey=%s",
            $config['azure']['storage.account'], $config['azure']['storage.key']);

        $this->blobRestProxy = ServicesBuilder::getInstance()->createBlobService($connectionString);
        $this->config = $config;
    }

    /**
     * @return BlobRestProxy
     */
    function factory()
    {
        return $this->blobRestProxy;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }
}