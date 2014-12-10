<?php
namespace CloudCopy\Origin\Resource;

use CloudCopy\Origin\FileNameBean;

class NameParser
{

    public function retrieveEntityBean($resourcePath)
    {
        preg_match('/^[A-Za-z._-]+/', $resourcePath, $m);

        $nodeName = $m[0];
        $entityName = preg_replace('/^[A-Za-z._-]+.\//', '', $resourcePath);

        $bean = new FileNameBean();
        $bean->setNode($nodeName);
        $bean->setEntity($entityName);

        return $bean;
    }
}