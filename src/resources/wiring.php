<?php

use Symfony\Component\DependencyInjection\Reference;

$container->register('configResource', 'Symfony\Component\Config\FileLocator')
    ->addArgument(__DIR__);
$container->register('ymlParser', 'Symfony\Component\Yaml\Parser');

$resource = $container->get('configResource')->locate('config.yml', null, false);
$config = $container->get('ymlParser')->parse(file_get_contents($resource[0]));
$container->register('configuration', 'CloudCopy\Configuration')
    ->addArgument($config);

$container->register('s3client', 'CloudCopy\AWS\S3Client')
    ->addArgument($config);
$container->register('sqsClient', 'CloudCopy\AWS\SqsClient')
    ->addArgument($config);
$container->register('storageClient', 'CloudCopy\Azure\StorageClient')
    ->addArgument($config);

$container->register('amazonS3ToAzureStorage', 'CloudCopy\AmazonS3ToAzureStorage')
    ->addArgument(new Reference('s3client'))
    ->addArgument(new Reference('storageClient'))
    ->addArgument(new Reference('awsResource'))
    ->addArgument(new Reference('blobCopy'))
    ->addArgument(new Reference('sqsClient'))
    ->addArgument($config);
$container->register('amazonS3ToFtpStorage', 'CloudCopy\AmazonS3ToFtpStorage')
    ->addArgument(new Reference('s3client'))
    ->addArgument(new Reference('awsResource'))
    ->addArgument(new Reference('ftpCopy'))
    ->addArgument(new Reference('sqsClient'))
    ->addArgument($config);

$container->register('localStorage', 'CloudCopy\Origin\LocalStorage')
    ->addArgument($config);
$container->register('sqsStorage', 'CloudCopy\Origin\SqsSource')
    ->addArgument(new Reference('sqsClient'))
    ->addArgument(new Reference('originResourceParser'))
    ->addArgument($config);

$container->register('awsResource', 'CloudCopy\AWS\DownloadResource')
    ->addArgument($config);
$container->register('blobCopy', 'CloudCopy\Azure\BlobCopy')
    ->addArgument(new Reference('storageClient'))
    ->addArgument($config);

$container->register('ftpCopy', 'CloudCopy\Ftp\BlobCopy')
    ->addArgument($config);

$container->register('originResourceParser', '\CloudCopy\Origin\Resource\NameParser');

$container->register('renameS3Bucket', 'CloudCopy\RenameS3Bucket')
    ->addArgument(new Reference('s3client'))
    ->addArgument(new Reference('storageClient'))
    ->addArgument(new Reference('awsResource'))
    ->addArgument(new Reference('sqsClient'))
    ->addArgument($config);

$container->compile();