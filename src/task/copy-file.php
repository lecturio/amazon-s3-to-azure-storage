<?php
#!/usr/bin/env php

require_once __DIR__ . '../../../bootstrap.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;


class CopyFile extends Command
{

    /**
     * @var Container
     */
    private $container;

    const AMAZON_S3_TO_AZURE_STORAGE = 's3-azure';

    protected function configure()
    {
        $this
            ->setName('copy:files')
            ->addOption(
                self::AMAZON_S3_TO_AZURE_STORAGE,
                null,
                InputOption::VALUE_NONE,
                'Copy files from Amazon s3 to Azure Storage'
            )
            ->setDescription('copy files across cloud services');

        $this->container = $GLOBALS['container'];
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption(self::AMAZON_S3_TO_AZURE_STORAGE)) {
            /**
             * @var $s3ToAzure \CloudCopy\AmazonS3ToAzureStorage
             */
            $s3ToAzure = $this->container->get('amazonS3ToAzureStorage');

            //$s3ToAzure->execute();

            /**
             * @var $sqsSource \CloudCopy\Origin\SqsSource
             */
            $started = microtime(true);
//            while (true) {
            $sqsSource = $this->container->get('sqsStorage');
            //$s3ToAzure->setEntitiesForCopy($sqsSource->retrieve());

            $sqsSource->retrieve();
            //$s3ToAzure->execute();
//                var_dump($sqsSource->retrieve());
//
//                if (time() > $started + 20 && count($sqsSource->retrieve()) == 0) {
//                    break;
//                }
//            }
        }
    }
}

$application = new Application();
$application->add(new \CopyFile);
$application->run();