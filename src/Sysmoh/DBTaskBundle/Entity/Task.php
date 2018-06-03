<?php

namespace Sysmoh\DBTaskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Table(name="sysmoh_db_task")
 * @ORM\Entity()
 */
class Task
{
    const   WAITING = 'waiting';
    const   SUCCESS = 'success';
    const   FAILED  = 'failed';

    use TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(name="params", type="text")
     */
    protected $params;

    /**
     * @var string
     * @ORM\Column(name="status", type="string", length=255)
     */
    protected $status;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return unserialize($this->params);
    }

    /**
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->params = serialize($params);
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}
