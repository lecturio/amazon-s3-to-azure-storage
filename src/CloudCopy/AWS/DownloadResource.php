<?php
namespace CloudCopy\AWS;


use CloudCopy\Origin\FileNameBean;

class DownloadResource
{
    private $config;

    function __construct($config)
    {
        $this->config = $config;
    }

    public function download($url, FileNameBean $entity)
    {
        $folder = dirname(sprintf('%s/%s', $entity->getNode(), $entity->getEntity()));
        $file = basename(sprintf('%s/%s', $entity->getNode(), $entity->getEntity()));
        $destination = sprintf('%s/%s', $this->config['temp.cloud.store'], $folder);

        if (!file_exists($destination)) {
            mkdir($destination, 0777, true);
            chmod($destination, 0777);
        }

        $ch = curl_init($url);
        $fp = fopen(sprintf('%s/%s', $destination, $file), 'wb');

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        if (preg_match('/./', $entity->getNode())) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
        }

        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
    }

    public function delete(FileNameBean $entity)
    {
        $destination = sprintf('%s/%s/%s', $this->config['temp.cloud.store'],
            $entity->getNode(), $entity->getEntity());
        unlink($destination);
    }

}