<?php

namespace GalerieBundle\Model;

use GalerieBundle\Entity\Media;
use GalerieBundle\Util\WebdavTrait;

class NCNode
{
    use WebdavTrait;

    private $etag;

    private $filename;

    private $webdavUrl;

    private $size;

    private $mimetype;

    public function __construct(array $data)
    {
        $this->etag         = $data['etag'];
        $this->filename     = $data['filename'];
        $this->webdavUrl    = $data['webdavUrl'];
        $this->size         = $data['size'];
        $this->mimetype     = $data['mimetype'];
    }

    public function toMedia() {

        $media = new Media();
        $media->setWebdavUrl($this->getWebdavUrl());
        $media->setFilename($this->getFilename());
        $media->setMimetype($this->getMimetype());
        $media->setEtag($this->getEtag());
        $media->setSize($this->getSize());

        return $media;
    }

    /**
     * @return mixed
     */
    public function getMimetype()
    {
        return $this->mimetype;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return intval($this->size);
    }

    /**
     * @return mixed
     */
    public function getWebdavUrl()
    {
        return $this->webdavUrl;
    }

    /**
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return mixed
     */
    public function getEtag()
    {
        return $this->etag;
    }
}