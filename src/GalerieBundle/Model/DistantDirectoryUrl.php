<?php

namespace GalerieBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

class DistantDirectoryUrl
{
    /**
     * @var string
     * @Assert\Regex(pattern="/^\/galerie\//", match=true,
     *      message="Le chemin doit commencer par /galerie/")
     */
    private $url = "/galerie/";

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }
}