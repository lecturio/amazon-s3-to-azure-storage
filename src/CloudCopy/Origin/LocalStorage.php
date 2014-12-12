<?php
namespace CloudCopy\Origin;


class LocalStorage implements EntitySource
{
    const COPY_LIST = "copy-list.txt";
    private $config;

    function __construct($config)
    {
        $this->config = $config;
    }

    public function retrieve()
    {
        $entities = array();

        $filePath = sprintf('%s/%s', $this->config['local.sourcelist'], self::COPY_LIST);
        foreach (file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $entity = explode('/', $line);

            //FIXME checking parser names
            $filename = new FileNameBean();
            $filename->setNode($entity[0]);
            $filename->setEntity($entity[1]);
            $entities[] = $filename;
        }

        return $entities;
    }


    public function cleanUp()
    {

    }

    public function cleanFailed()
    {

    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
    }
}