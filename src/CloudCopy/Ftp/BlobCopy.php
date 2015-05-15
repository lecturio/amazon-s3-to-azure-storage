<?php

namespace CloudCopy\Ftp;

use CloudCopy\Origin\FileNameBean;

/**
 * Copy files to ftp provider.
 *
 * Class BlobCopy
 * @package CloudCopy\Ftp
 */
class BlobCopy
{

    private $config;

    function __construct($config)
    {
        $this->config = $config;
    }

    public function copy(FileNameBean $entity)
    {
        $destination = sprintf('%s/%s', $this->config['temp.cloud.store'], $entity->getNode());
        $content = sprintf('%s/%s', $destination, $entity->getEntity());

        $fileName = str_replace(array('o-hd-', 'o-ld-', 'o-sd-'), 'o-', $entity->getEntity());
        $fileName = preg_replace('/(st1|st2|st3)+.*$/', sprintf('%s.%s', $entity->getBitRate(), 'mp4'),
            $fileName);

        $container = parse_url($this->config['backups']['aws.to.ftp'][$entity->getNode()]);

        $ftpConn = ftp_connect($container['host']);
        $login = ftp_login($ftpConn, $container['user'], $container['pass']);

        if ((!$ftpConn) || (!$login)) {
            throw new \Exception('Can\'t connect to ftp');
        }

        ftp_pasv($ftpConn, true);
        $upload = ftp_put($ftpConn, sprintf('%s/%s', $container['path'], $fileName), $content, FTP_BINARY);
        if (!$upload) {
            throw new \Exception(sprintf("File %s can't be uploaded \n", $content));
        }

    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
    }
}