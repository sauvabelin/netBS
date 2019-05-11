<?php

namespace Ovesco\GalerieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * DirectoryView
 *
 * @ORM\Table(name="ovesco_galerie_directory_view")
 * @ORM\Entity
 */
class DirectoryView
{
    use TimestampableEntity;

    const MOBILE = 'mobile';
    const TABLETTE = 'tablette';
    const ORDINATEUR = 'ordinateur';

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
     *
     * @ORM\Column(name="path", type="string", length=255)
     */
    protected $path;

    /**
     * @var string
     *
     * @ORM\Column(name="device", type="string", length=255)
     */
    protected $device;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set path.
     *
     * @param string $path
     *
     * @return DirectoryView
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getDevice()
    {
        return $this->device;
    }

    /**
     * @param string $device
     * @return self
     */
    public function setDevice($device)
    {
        $this->device = $device;

        return $this;
    }
}
