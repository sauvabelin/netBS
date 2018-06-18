<?php

namespace GalerieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use GalerieBundle\Util\WebdavTrait;

/**
 * Media
 *
 * @ORM\Table(name="netbs_galerie_media")
 * @ORM\Entity(repositoryClass="GalerieBundle\Repository\MediaRepository")
 */
class Media
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="etag", type="string", length=255, unique=true)
     */
    private $etag;

    /**
     * @var int
     *
     * @ORM\Column(name="size", type="integer")
     */
    private $size;

    /**
     * @var string
     *
     * @ORM\Column(name="mimetype", type="string", length=255)
     */
    private $mimetype;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=255)
     */
    private $filename;

    /**
     * @var string
     *
     * @ORM\Column(name="webdavUrl", type="string", length=255)
     */
    private $webdavUrl;

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
     * Set etag.
     *
     * @param string $etag
     *
     * @return Media
     */
    public function setEtag($etag)
    {
        $this->etag = $etag;

        return $this;
    }

    /**
     * Get etag.
     *
     * @return string
     */
    public function getEtag()
    {
        return $this->etag;
    }

    /**
     * Set size.
     *
     * @param int $size
     *
     * @return Media
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size.
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set mimetype.
     *
     * @param string $mimetype
     *
     * @return Media
     */
    public function setMimetype($mimetype)
    {
        $this->mimetype = $mimetype;

        return $this;
    }

    /**
     * Get mimetype.
     *
     * @return string
     */
    public function getMimetype()
    {
        return $this->mimetype;
    }

    /**
     * Set filename.
     *
     * @param string $filename
     *
     * @return Media
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set webdavUrl.
     *
     * @param string $webdavUrl
     *
     * @return Media
     */
    public function setSearchPath($webdavUrl)
    {
        $this->webdavUrl = $webdavUrl;

        return $this;
    }

    /**
     * Get webdavUrl.
     *
     * @return string
     */
    public function getSearchPath()
    {
        return $this->webdavUrl;
    }
}
