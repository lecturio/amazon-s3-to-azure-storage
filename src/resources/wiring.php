<?php

use Symfony\Component\DependencyInjection\Reference;

$container->register('resourceLocator', 'Symfony\Component\Config\FileLocator')
    ->addArgument(__DIR__);
$container->register('ymlParser', 'Symfony\Component\Yaml\Parser');

$resource = $container->get('resourceLocator')->locate('config.yml', null, false);
$config = $container->get('ymlParser')->parse(file_get_contents($resource[0]));
$container->register('s3client', 'CloudCopy\AWS\S3Client')
    ->addArgument($config);

$container->register('amazonS3ToAzureStorage', 'CloudCopy\AmazonS3ToAzureStorage')
    ->addArgument(new Reference('s3client'))
    ->addArgument($config);

$container->compile();