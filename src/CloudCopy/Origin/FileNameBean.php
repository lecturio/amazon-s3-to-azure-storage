<?php
namespace CloudCopy\Origin;

/**
 * Model for filename bean.
 *
 * Class FileNameBean
 * @package CloudCopy\Origin
 */
class FileNameBean
{

    /**
     *
     * @var string
     */
    private $node;

    /**
     * @return string
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * @param string $node
     */
    public function setNode($node)
    {
        $this->node = $node;
    }

    /**
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param string $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @var string
     */
    private $entity;

}