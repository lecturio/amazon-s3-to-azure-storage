<?php
#!/usr/bin/env php

require_once __DIR__ . '../../../bootstrap.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;


class CopyFile extends Command
{

    const AMAZON_S3_TO_AZURE_STORAGE = 's3-azure';
    const AMAZON_S3_TO_FTP = 's3-ftp';
    const CLEANUP = 'cleanup';
    const RENAME_S3_BUCKET = 'rename-s3';
    /**
     * @var Container
     */
    private $container;

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
            ->addOption(
                self::AMAZON_S3_TO_FTP,
                null,
                InputOption::VALUE_NONE,
                'Copy files from Amazon s3 to Ftp Storage'
            )
            ->addOption(
                self::CLEANUP,
                null,
                InputOption::VALUE_NONE,
                'Clean temp files from previous run'
            )
            ->addOption(
                self::RENAME_S3_BUCKET,
                null,
                InputOption::VALUE_NONE,
                'Rename s3 bucket to new bucket'
            )
            ->setDescription('copy files across cloud services');

        $this->container = $GLOBALS['container'];
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption(self::CLEANUP)) {
            $resource = $this->container->get('configResource')->locate('config.yml', null, false);
            $config = $this->container->get('ymlParser')->parse(file_get_contents($resource[0]));
            foreach (glob($config['temp.cloud.store'] . '/*') as $node) {
                if (is_dir($node)) {
                    exec('rm -rf ' . $node);
                }
            }
        }

        if ($input->getOption(self::AMAZON_S3_TO_AZURE_STORAGE)) {
            $this->amazonToAzure($input, $output);
        }

        if ($input->getOption(self::AMAZON_S3_TO_FTP)) {
            $this->amazonToFtp($input, $output);
        }

        if ($input->getOption(self::RENAME_S3_BUCKET)) {
            /**
             * @var $renameS3Bucket \CloudCopy\renameS3Bucket
             * @var $sqsSource \CloudCopy\Origin\SqsSource
             */
            $renameS3Bucket = $this->container->get('renameS3Bucket');
            $renameS3Bucket->setOutput($output);
            $sqsSource = $this->container->get('sqsStorage');

            static $started;
            $i = 0;
            while (true) {
                ++$i;

                if ($i % 101 == 0) {
                    sleep(1);
                    $i = 0;
                }

                $messages = $sqsSource->retrieve();
                $messagesLength = count($messages);

                if ($messagesLength > 0) {
                    $started = null;

                    $renameS3Bucket->setEntitiesForCopy($messages);
                    if ($renameS3Bucket->execute()) {
                        $sqsSource->cleanUp();
                    } else {
                        $sqsSource->cleanFailed();
                    }
                }

                if ($messagesLength == 0) {
                    if ($started === null) {
                        $started = time();
                    }

                    if (time() > $started + 5400) {
                        break;
                    }
                }
            }
        }
    }

    private function amazonToAzure(InputInterface $input, OutputInterface $output)
    {
        /**
         * @var $s3ToAzure \CloudCopy\AmazonS3ToAzureStorage
         * @var $sqsSource \CloudCopy\Origin\SqsSource
         */
        $s3ToAzure = $this->container->get('amazonS3ToAzureStorage');
        $s3ToAzure->setOutput($output);
        $sqsSource = $this->container->get('sqsStorage');

        /**
         * @var $sqsSource \CloudCopy\Origin\SqsSource
         */

        static $started;
        $i = 0;
        while (true) {
            ++$i;

            if ($i % 101 == 0) {
                sleep(1);
            }

            $messages = $sqsSource->retrieve();
            $messagesLength = count($messages);

            if ($messagesLength > 0) {
                $started = null;

                $s3ToAzure->setEntitiesForCopy($messages);
                if ($s3ToAzure->execute()) {
                    $sqsSource->cleanUp();
                } else {
                    $sqsSource->cleanFailed();
                }
            }

            if ($messagesLength == 0) {
                if ($started === null) {
                    $started = time();
                }

                if (time() > $started + 5400) {
                    break;
                }
            }
        }
    }

    private function amazonToFtp($input, $output)
    {
        /**
         * @var $s3ToAzure \CloudCopy\AmazonS3ToFtpStorage
         * @var $sqsSource \CloudCopy\Origin\SqsSource
         */
        $s3ToAzure = $this->container->get('amazonS3ToFtpStorage');
        $s3ToAzure->setOutput($output);
        $sqsSource = $this->container->get('sqsStorage');

        /**
         * @var $sqsSource \CloudCopy\Origin\SqsSource
         */

        static $started;
        $i = 0;
        while (true) {
            ++$i;

            if ($i % 101 == 0) {
                sleep(1);
            }

            $messages = $sqsSource->retrieve();
            $messagesLength = count($messages);

            if ($messagesLength > 0) {
                $started = null;

                $s3ToAzure->setEntitiesForCopy($messages);
                if ($s3ToAzure->execute()) {
                    $sqsSource->cleanUp();
                } else {
                    $sqsSource->cleanFailed();
                }
            }

            if ($messagesLength == 0) {
                if ($started === null) {
                    $started = time();
                }

                if (time() > $started + 5400) {
                    break;
                }
            }
        }
    }

}

$application = new Application();
$application->add(new \CopyFile);
$application->run();