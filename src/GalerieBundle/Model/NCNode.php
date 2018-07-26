<?php

namespace GalerieBundle\Model;

use GalerieBundle\Entity\Media;
use GalerieBundle\Util\WebdavTrait;

class NCNode implements \JsonSerializable
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
        $this->filename     = $data['name'];
        $this->webdavUrl    = $data['path'];
        $this->size         = $data['size'];
        $this->mimetype     = $data['mimetype'];
    }

    public function jsonSerialize()
    {
        return [
            "name"      => $this->filename,
            "path"      => $this->webdavUrl,
            "size"      => $this->size,
            "etag"      => $this->etag,
            "mimetype"  => $this->mimetype
        ];
    }

    public function toMedia() {

        $media = new Media();
        $media->setSearchPath($this->getsearchPath());
        $media->setFilename($this->getFilename());
        $media->setMimetype($this->getMimetype());
        $media->setEtag($this->getEtag());
        $media->setSize($this->getSize());

        return $media;
    }

    public function isDirectory() {
        return $this->mimetype === "httpd/unix-directory";
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