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
            @mkdir($destination, 0777, true);
            chmod($destination, 0777);
        }

        $p = @fopen($url, 'r');
        if ($p) {
            file_put_contents(sprintf('%s/%s', $destination, $file), $p);
        }

        return $p;
    }

    public function delete(FileNameBean $entity)
    {
        $destination = sprintf('%s/%s/%s', $this->config['temp.cloud.store'],
            $entity->getNode(), $entity->getEntity());
        unlink($destination);
    }

}