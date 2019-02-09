<?php

namespace SauvabelinBundle\Entity;

use NetBS\FichierBundle\Mapping\BaseAttribution;
use NetBS\FichierBundle\Mapping\BaseMembre;
use Doctrine\ORM\Mapping as ORM;
use NetBS\CoreBundle\Validator\Constraints as BSAssert;

/**
 * Class Membre
 * @package SauvabelinBundle\Entity
 * @ORM\Entity()
 * @ORM\Table(name="sauvabelin_netbs_membres")
 * @BSAssert\User(rules={
 *     "numeroBS":"user.hasRole('ROLE_SG')"
 * })
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

    public function setStatut($statut)
    {
        $this->statut = $statut;
        $close = [];
        if($statut === self::DECEDE)
            foreach ($this->getActivesAttributions() as $attribution)
                $close[] = $attribution;
        else if(in_array($statut, [self::DECEDE, self::AUTRE]))
            foreach ($this->getActivesAttributions() as $attribution)
                if (strtolower($attribution->getGroupe()->getNom()) !== 'adabs')
                    $close[] = $attribution;

        /** @var BaseAttribution $attribution */
        foreach($close as $attribution)
            $attribution->setDateFin(new \DateTime());

        return $this;
    }
}
