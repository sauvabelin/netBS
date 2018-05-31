<?php

namespace GalerieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class Media
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="nom", type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(name="webdav_uri", type="string", length=255)
     */
    protected $webdavUri;

    /**
     * @var string
     * @ORM\Column(name="media_uri", type="string", length=255)
     */
    protected $mediaUri;

    /**
     * @var string
     * @ORM\Column(name="etag", type="string", length=255)
     */
    protected $etag;

    /**
     * @var string
     * @ORM\Column(name="last_updated", type="string")
     */
    protected $lastUpdated;

    /**
     * @var Directory
     * @ORM\ManyToOne(targetEntity="App\Entity\Directory", inversedBy="medias")
     */
    protected $directory;

    /**
     * @var string
     * @ORM\Column(name="mime_type", type="string", length=255)
     */
    protected $mimeType;

    /**
     * @var int
     * @ORM\Column(name="file_size", type="integer")
     */
    protected $size;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getExtension() {

        $parts  = explode('.', $this->webdavUri);
        return array_pop($parts);
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

    /**
     * @return string
     */
    public function getEtag()
    {
        return $this->etag;
    }

    /**
     * @param string $etag
     */
    public function setEtag($etag)
    {
        $this->etag = $etag;
    }

    /**
     * @return string
     */
    public function getLastUpdated()
    {
        return $this->lastUpdated;
    }

    /**
     * @param string $lastUpdated
     */
    public function setLastUpdated($lastUpdated)
    {
        $this->lastUpdated = $lastUpdated;
    }

    /**
     * @return string
     */
    public function getWebdavUri()
    {
        return $this->webdavUri;
    }

    /**
     * @param string $webdavUri
     */
    public function setWebdavUri($webdavUri)
    {
        $this->webdavUri = $webdavUri;
    }

    /**
     * @return Directory
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * @param Directory $directory
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory;
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @param string $mimeType
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param int $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return string
     */
    public function getMediaUri()
    {
        return $this->mediaUri;
    }

    /**
     * @param string $mediaUri
     */
    public function setMediaUri($mediaUri)
    {
        $this->mediaUri = $mediaUri;
    }
}