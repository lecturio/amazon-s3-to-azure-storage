<?php
namespace CloudCopy\AWS;


use CloudCopy\Origin\FileNameBean;

class DownloadResource
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function download($url, FileNameBean $entity)
    {
        $destination = sprintf('%s/%s', $this->config['temp.cloud.store'], $entity->getNode());

        if (!file_exists($destination)) {
            mkdir($destination, 0777, true);
            chmod($destination, 0777);
        }

        $ch = curl_init($url);
        $fp = fopen(sprintf('%s/%s', $destination, $entity->getEntity()), 'wb');

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        if (preg_match('/./', $entity->getNode())) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
        }

        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
    }

}