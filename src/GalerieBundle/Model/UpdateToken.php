<?php

namespace GalerieBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateToken
{
    /**
     * @Assert\Regex(pattern="/^[a-z\-0-9]+$/i", match=true,
     *      message="La clé ne peut contenir que des chiffres et des lettres")
     */
    private $token;

    public function __construct($token)
    {
        $this->token    = $token;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }
}