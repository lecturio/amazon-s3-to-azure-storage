<?php

use Symfony\Component\DependencyInjection\Reference;

$container->register('configResource', 'Symfony\Component\Config\FileLocator')
    ->addArgument(__DIR__);
$container->register('ymlParser', 'Symfony\Component\Yaml\Parser');

$resource = $container->get('configResource')->locate('config.yml', null, false);
$config = $container->get('ymlParser')->parse(file_get_contents($resource[0]));
$container->register('s3client', 'CloudCopy\AWS\S3Client')
    ->addArgument($config);
$container->register('storageClient', 'CloudCopy\Azure\StorageClient')
    ->addArgument($config);

$container->register('amazonS3ToAzureStorage', 'CloudCopy\AmazonS3ToAzureStorage')
    ->addArgument(new Reference('s3client'))
    ->addArgument(new Reference('storageClient'))
    ->addArgument(new Reference('localStorage'))
    ->addArgument(new Reference('awsResource'))
    ->addArgument(new Reference('blobCopy'))
    ->addArgument($config);

$container->register('localStorage', 'CloudCopy\Origin\LocalStorage')
    ->addArgument($config);

$container->register('awsResource', 'CloudCopy\AWS\DownloadResource')
    ->addArgument($config);
$container->register('blobCopy', 'CloudCopy\Azure\BlobCopy')
    ->addArgument(new Reference('storageClient'))
    ->addArgument($config);

$container->compile();