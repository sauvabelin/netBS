<?php

namespace Ovesco\FacturationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use NetBS\FichierBundle\Utils\Entity\RemarqueTrait;
use Ovesco\FacturationBundle\Util\DebiteurTrait;

/**
 * Facture
 *
 * @ORM\Table(name="facture")
 * @ORM\Entity(repositoryClass="Ovesco\FacturationBundle\Repository\FactureRepository")
 */
class Facture
{
    const PAYEE     = 'payee';
    const OUVERTE   = 'ouverte';
    const ANNULEE   = 'annulee';

    use TimestampableEntity, RemarqueTrait, DebiteurTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="statut", type="string", length=255)
     */
    protected $statut;

    /**
     * @var Creance[]
     *
     * @ORM\OneToMany(targetEntity="Ovesco\FacturationBundle\Entity\Creance", mappedBy="facture")
     */
    protected $creances;

    /**
     * @var Rappel[]
     *
     * @ORM\OneToMany(targetEntity="Ovesco\FacturationBundle\Entity\Rappel", mappedBy="facture")
     */
    protected $rappels;

    /**
     * @var Paiement[]
     *
     * @ORM\OneToMany(targetEntity="Ovesco\FacturationBundle\Entity\Paiement", mappedBy="facture")
     */
    protected $paiements;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->creances = new \Doctrine\Common\Collections\ArrayCollection();
        $this->rappels = new \Doctrine\Common\Collections\ArrayCollection();
        $this->paiements = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set statut.
     *
     * @param string $statut
     *
     * @return Facture
     */
    public function setStatut($statut)
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * Get statut.
     *
     * @return string
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * Add creance.
     *
     * @param \Ovesco\FacturationBundle\Entity\Creance $creance
     *
     * @return Facture
     */
    public function addCreance(\Ovesco\FacturationBundle\Entity\Creance $creance)
    {
        $this->creances[] = $creance;

        return $this;
    }

    /**
     * Remove creance.
     *
     * @param \Ovesco\FacturationBundle\Entity\Creance $creance
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeCreance(\Ovesco\FacturationBundle\Entity\Creance $creance)
    {
        return $this->creances->removeElement($creance);
    }

    /**
     * Get creances.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCreances()
    {
        return $this->creances;
    }

    /**
     * Add rappel.
     *
     * @param \Ovesco\FacturationBundle\Entity\Rappel $rappel
     *
     * @return Facture
     */
    public function addRappel(\Ovesco\FacturationBundle\Entity\Rappel $rappel)
    {
        $this->rappels[] = $rappel;

        return $this;
    }

    /**
     * Remove rappel.
     *
     * @param \Ovesco\FacturationBundle\Entity\Rappel $rappel
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeRappel(\Ovesco\FacturationBundle\Entity\Rappel $rappel)
    {
        return $this->rappels->removeElement($rappel);
    }

    /**
     * Get rappels.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRappels()
    {
        return $this->rappels;
    }

    /**
     * Add paiement.
     *
     * @param \Ovesco\FacturationBundle\Entity\Paiement $paiement
     *
     * @return Facture
     */
    public function addPaiement(\Ovesco\FacturationBundle\Entity\Paiement $paiement)
    {
        $this->paiements[] = $paiement;

        return $this;
    }

    /**
     * Remove paiement.
     *
     * @param \Ovesco\FacturationBundle\Entity\Paiement $paiement
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removePaiement(\Ovesco\FacturationBundle\Entity\Paiement $paiement)
    {
        return $this->paiements->removeElement($paiement);
    }

    /**
     * Get paiements.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPaiements()
    {
        return $this->paiements;
    }
}
