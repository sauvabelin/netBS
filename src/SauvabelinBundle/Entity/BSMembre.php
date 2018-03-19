<?php

namespace SauvabelinBundle\Entity;

use NetBS\FichierBundle\Mapping\BaseMembre;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Membre
 * @package SauvabelinBundle\Entity
 * @ORM\Entity()
 * @ORM\Table(name="sauvabelin_netbs_membres")
 */
class BSMembre extends BaseMembre
{
    /**
     * @var integer
     * @ORM\Column(name="numero_bs", type="integer", length=30, nullable=true)
     */
    protected $numeroBS;

    /**
     * @return int
     */
    public function getNumeroBS()
    {
        return $this->numeroBS;
    }

    /**
     * @param int $numeroBS
     * @return $this
     */
    public function setNumeroBS($numeroBS)
    {
        $this->numeroBS = $numeroBS;
        return $this;
    }
}