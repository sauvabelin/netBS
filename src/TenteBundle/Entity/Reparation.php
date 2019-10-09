<?php

namespace TenteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use NetBS\FichierBundle\Utils\Entity\RemarqueTrait;
use TenteBundle\Model\ReparationPartie;

/**
 * Reparation
 *
 * @ORM\Table(name="tente_reparations")
 * @ORM\Entity
 */
class Reparation
{
    use TimestampableEntity, RemarqueTrait;

    const EN_COURS = 'en_cours';
    const RECEPTIONNEE = 'receptionnee';

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
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status = self::EN_COURS;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="envoyee", type="date")
     */
    private $envoyee;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="recue", type="date", nullable=true)
     */
    private $recue;

    /**
     * @var Tente
     *
     * @ORM\ManyToOne(targetEntity="Tente", inversedBy="reparations")
     */
    private $tente;

    /**
     * @var array
     *
     * @ORM\Column(name="parties", type="array")
     */
    private $parties;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public static function getStatusChoices() {
        return [
            self::EN_COURS => 'En cours',
            self::RECEPTIONNEE => 'Réceptionnée',
        ];
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->feuillesEtat = new \Doctrine\Common\Collections\ArrayCollection();
        $this->envoyee = new \DateTime();
    }

    /**
     * Set tente.
     *
     * @param \TenteBundle\Entity\Tente|null $tente
     *
     * @return Reparation
     */
    public function setTente(\TenteBundle\Entity\Tente $tente = null)
    {
        $this->tente = $tente;
        $tente->addReparation($this);
        return $this;
    }

    /**
     * Get tente.
     *
     * @return \TenteBundle\Entity\Tente|null
     */
    public function getTente()
    {
        return $this->tente;
    }

    /**
     * Add feuillesEtat.
     *
     * @param \TenteBundle\Entity\FeuilleEtat $feuillesEtat
     *
     * @return Reparation
     */
    public function addFeuillesEtat(\TenteBundle\Entity\FeuilleEtat $feuillesEtat)
    {
        $this->feuillesEtat[] = $feuillesEtat;

        return $this;
    }

    /**
     * Remove feuillesEtat.
     *
     * @param \TenteBundle\Entity\FeuilleEtat $feuillesEtat
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeFeuillesEtat(\TenteBundle\Entity\FeuilleEtat $feuillesEtat)
    {
        return $this->feuillesEtat->removeElement($feuillesEtat);
    }

    /**
     * Get feuillesEtat.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFeuillesEtat()
    {
        return $this->feuillesEtat;
    }

    /**
     * @return \DateTime
     */
    public function getRecue()
    {
        return $this->recue;
    }

    /**
     * @param \DateTime $recue
     */
    public function setRecue($recue)
    {
        $this->recue = $recue;
        $this->setStatus(self::RECEPTIONNEE);
    }

    /**
     * @return \DateTime
     */
    public function getEnvoyee()
    {
        return $this->envoyee;
    }

    /**
     * @param \DateTime $envoyee
     */
    public function setEnvoyee($envoyee)
    {
        $this->envoyee = $envoyee;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return ReparationPartie[]
     */
    public function getParties()
    {
        return $this->parties;
    }

    /**
     * @param ReparationPartie[] $parties
     */
    public function setParties($parties)
    {
        $this->parties = $parties;
    }
}
