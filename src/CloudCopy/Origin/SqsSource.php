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

    private $result;

    function __construct(SqsClient $sqsClient, NameParser $nameParser, $config)
    {
        $this->client = $sqsClient->factory();
        $this->nameParser = $nameParser;

        $this->config = $config;
    }

    public function retrieve()
    {
        $entities = array();

        $this->result = $this->client->receiveMessage(array(
            'QueueUrl' => $this->config['aws']['sqs.pool.url'],
            'WaitTimeSeconds' => 10,
            'MaxNumberOfMessages' => 1,
            'VisibilityTimeout' => 3600,
            'AttributeNames' => array('ApproximateReceiveCount')
        ));

        if ($this->result->getPath('Messages/*/Body')) {
            foreach ($this->result->getPath('Messages/*/Body') as $messageBody) {
                $entities[] = $this->nameParser->retrieveEntityBean($messageBody);
            }
        }

        return $entities;
    }

    public function cleanFailed()
    {
        if (!$this->result->getPath('Messages/*/Attributes')) {
            return;
        }

        $attributes = $this->result->getPath('Messages/*/Attributes');

        if (isset($attributes['ApproximateReceiveCount'])
            && (int)$attributes['ApproximateReceiveCount'] > (int)$this->config['retries']
        ) {
            $this->cleanUp();
        }
    }

    public function cleanUp()
    {
        if (!$this->result->getPath('Messages/*/ReceiptHandle')) {
            return;
        }

        foreach ($this->result->getPath('Messages/*/ReceiptHandle') as $receiptHandle) {
            $this->client->deleteMessage(array(
                'QueueUrl' => $this->config['aws']['sqs.pool.url'],
                'ReceiptHandle' => $receiptHandle
            ));
        }
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
