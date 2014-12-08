<?php
namespace CloudCopy\Azure;


use WindowsAzure\Blob\BlobRestProxy;

/**
 * Setup containers before initial copy of the data.
 *
 * Class Container
 * @package CloudCopy\Azure
 */
class Container
{
    /**
     * @var BlobRestProxy
     */
    private $storageClient;

    private $config;

    function __construct(StorageClient $storageClient, $config = array())
    {
        $this->storageClient = $storageClient->factory();
        $this->config = $config;
    }

    function create()
    {
        foreach ($this->config['azure']['storage.containers'] as $container) {
            //TODO add permissions
            $this->storageClient->createContainer($container);
        }
    }
}