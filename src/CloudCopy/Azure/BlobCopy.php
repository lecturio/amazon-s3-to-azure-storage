<?php
namespace CloudCopy\Azure;


use CloudCopy\Origin\FileNameBean;

class BlobCopy
{

    /**
     * @var \WindowsAzure\Blob\BlobRestProxy
     */
    private $client;

    private $config;

    function __construct(StorageClient $client, $config)
    {
        $this->client = $client->factory();
        $this->config = $config;
    }

    public function copy(FileNameBean $entity)
    {
        $destination = sprintf('%s/%s', $this->config['temp.cloud.store'], $entity->getNode());
        $content = fopen(sprintf('%s/%s', $destination, $entity->getEntity()), 'r');

        if (isset($this->config['backups']['aws.to.azure'][$entity->getNode()])) {
            $container = $this->config['backups']['aws.to.azure'][$entity->getNode()];
            $this->client->createBlockBlob($container, $entity->getEntity(), $content);
        }
    }

    /**
     * @return \WindowsAzure\Blob\BlobRestProxy
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
    }

}