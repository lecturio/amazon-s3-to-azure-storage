<?php
namespace CloudCopy\AWS;

use Aws\Sqs\SqsClient as client;

class SqsClient
{

    /**
     * @var client
     */
    private $client;

    function __construct($config)
    {
        $this->client = client::factory(array(
            'key' => $config['aws']['access.key'],
            'secret' => $config['aws']['secret.key'],
            'region' => 'eu-west-1'
        ));
    }

    /**
     * @return client
     */
    public function factory()
    {
        return $this->client;
    }
}
