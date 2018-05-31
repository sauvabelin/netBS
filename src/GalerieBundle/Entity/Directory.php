<?php

namespace GalerieBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Directory
 * @ORM\Entity
 * @ORM\Table(name="galeriebs_directories")
 */
class Directory
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
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var string
     * @ORM\Column(name="etag", type="string", length=255)
     */
    protected $etag;

    /**
     * @var string
     * @ORM\Column(name="last_updated", type="string", length=255)
     */
    protected $lastUpdated;

    /**
     * @var Directory
     * @ORM\ManyToOne(targetEntity="App\Entity\Directory", inversedBy="enfants")
     */
    protected $parent;

    /**
     * @var Directory[]
     * @ORM\OneToMany(targetEntity="App\Entity\Directory", mappedBy="parent")
     */
    protected $enfants;

    /**
     * @var Media[]
     * @ORM\OneToMany(targetEntity="App\Entity\Media", mappedBy="directory")
     */
    protected $medias;

    public function __construct()
    {
        $this->enfants  = new ArrayCollection();
        $this->medias   = new ArrayCollection();
    }

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
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
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
     * @return Directory
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Directory $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @param Directory $enfant
     */
    public function addEnfant(Directory $enfant) {

        $this->enfants[]    = $enfant;
        $enfant->setParent($this);
    }

    /**
     * @param Directory $enfant
     */
    public function removeEnfant(Directory $enfant) {

        $this->enfants->removeElement($enfant);
    }

    /**
     * @return Directory[]
     */
    public function getEnfants()
    {
        return $this->enfants;
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
     * @return Media[]
     */
    public function getMedias()
    {
        return $this->medias;
    }

    /**
     * @param Media $media
     */
    public function addMedia(Media $media) {

        $this->medias[] = $media;
    }

    /**
     * @param Media $media
     */
    public function removeMedia(Media $media) {

        $this->medias->removeElement($media);
    }
}