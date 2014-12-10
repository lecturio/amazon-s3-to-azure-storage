<?php
namespace CloudCopy\Origin;

use Aws\Sqs\SqsClient as client;
use CloudCopy\AWS\SqsClient;
use CloudCopy\Origin\Resource\NameParser;

/**
 * Amazon message queue service
 *
 * Class SQSSource
 * @package CloudCopy\Origin
 */
class SqsSource implements EntitySource
{
    /**
     * @var client
     */
    private $client;

    private $config;
    /**
     * @var $nameParser NameParser
     */
    private $nameParser;

    function __construct(SqsClient $sqsClient, NameParser $nameParser, $config)
    {
        $this->client = $sqsClient->factory();
        $this->nameParser = $nameParser;

        $this->config = $config;
    }

    function retrieve()
    {
        $entities = array();

        $result = $this->client->receiveMessage(array(
            'QueueUrl' => $this->config['aws']['sqs.pool.url'],
            'WaitTimeSeconds' => 10,
        ));

        if ($result->getPath('Messages/*/Body')) {
            foreach ($result->getPath('Messages/*/Body') as $messageBody) {
                $entities[] = $this->nameParser->retrieveEntityBean($messageBody);
            }
        }

        return $entities;
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return client
     */
    public function getClient()
    {
        return $this->client;
    }
}
