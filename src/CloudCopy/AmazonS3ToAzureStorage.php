<?php
namespace CloudCopy;


use Aws\S3\S3Client as client;
use Aws\Sqs\SqsClient as awsSqsClient;
use CloudCopy\AWS\DownloadResource;
use CloudCopy\AWS\S3Client;
use CloudCopy\AWS\SqsClient;
use CloudCopy\Azure\BlobCopy;
use CloudCopy\Azure\StorageClient;
use CloudCopy\Origin\FileNameBean;
use Symfony\Component\Console\Output\OutputInterface;
use WindowsAzure\Blob\BlobRestProxy;


class AmazonS3ToAzureStorage
{
    const LINK_AVAILABILITY_TIMEOUT = '20 minutes';

    /**
     * @var client
     */
    private $s3client;

    /**
     * @var BlobRestProxy
     */
    private $storageClient;

    /**
     * @var awsSqsClient
     */
    private $sqsClient;

    /**
     * @var DownloadResource
     */
    private $downloadResource;

    /**
     * @var BlobCopy
     */
    private $copyResource;

    private $config;

    private $entitiesForCopy;

    /**
     * @var OutputInterface
     */
    private $output;

    function __construct(
        S3Client $s3Client,
        StorageClient $storageClient,
        DownloadResource $downloadResource,
        BlobCopy $blobCopy,
        SqsClient $sqsClient,
        $config
    ) {
        $this->s3client = $s3Client->factory();
        $this->storageClient = $storageClient->factory();
        $this->downloadResource = $downloadResource;
        $this->copyResource = $blobCopy;
        $this->sqsClient = $sqsClient->factory();
        $this->config = $config;
    }

    function execute()
    {
        foreach ($this->entitiesForCopy as $entity) {
            /**
             * @var $entity FileNameBean
             */
            $entity->getEntity();
            $entity->getNode();
            $this->writeln(sprintf('Start download %s %s', $entity->getNode(), $entity->getEntity()));

            $url = $this->s3client->getObjectUrl($entity->getNode(), $entity->getEntity(),
                self::LINK_AVAILABILITY_TIMEOUT);

            if (preg_match('/./', $entity->getNode())) {
                $url = str_replace($entity->getNode() . '/', '', $url);
                $parsedUrl = parse_url($url);
                $url = sprintf('%s://%s.%s%s?%s', $parsedUrl['scheme'], $entity->getNode(), $parsedUrl['host'],
                    $parsedUrl['path'], $parsedUrl['query']);
            }

            if ($this->downloadResource->download($url, $entity) == false) {
                $this->writeln(sprintf('Missing %s %s', $entity->getNode(), $entity->getEntity()));
                return false;
            }

            try {
                $this->writeln(sprintf('Start copy %s %s', $entity->getNode(), $entity->getEntity()));
                $this->copyResource->copy($entity);
                $this->downloadResource->delete($entity);
                $this->writeln(sprintf('Clean %s %s', $entity->getNode(), $entity->getEntity()));
            } catch (\Exception $e) {
                $this->writeln($e->getMessage());
                return false;
            }
        }

        return true;
    }

    /**
     * @return OutputInterface
     */
    private function writeln($message)
    {
        if ($this->output !== null && $this->config['trace'] == true) {
            return $this->output->writeln($message);
        }
    }

    /**
     * @return client
     */
    public function getS3client()
    {
        return $this->s3client;
    }

    /**
     * @param mixed $entitiesForCopy
     */
    public function setEntitiesForCopy($entitiesForCopy)
    {
        $this->entitiesForCopy = $entitiesForCopy;
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput($output)
    {
        $this->output = $output;
    }
}
