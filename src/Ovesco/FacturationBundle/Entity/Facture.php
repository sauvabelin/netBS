<?php

namespace Ovesco\FacturationBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use NetBS\FichierBundle\Utils\Entity\RemarqueTrait;
use Ovesco\FacturationBundle\Util\DebiteurTrait;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Facture
 *
 * @ORM\Table(name="ovesco_facturation_factures")
 * @ORM\Entity
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
     * @Groups({"default"})
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="statut", type="string", length=255)
     * @Groups({"default"})
     */
    protected $statut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     * @Groups({"default"})
     */
    protected $date;

    /**
     * @var Creance[]
     *
     * @ORM\OneToMany(targetEntity="Creance", mappedBy="facture", fetch="EAGER")
     * @Groups({"facture_with_creances"})
     */
    protected $creances;

    /**
     * @var Rappel[]
     *
     * @ORM\OneToMany(targetEntity="Rappel", mappedBy="facture", cascade={"persist", "remove"}, fetch="EAGER")
     * @Groups({"default"})
     */
    protected $rappels;

    /**
     * @var Paiement[]
     *
     * @ORM\OneToMany(targetEntity="Paiement", mappedBy="facture", cascade={"persist", "remove"}, fetch="EAGER")
     * @Groups({"facture_with_paiements"})
     */
    protected $paiements;

    /**
     * @var Compte
     *
     * @ORM\ManyToOne(targetEntity="Ovesco\FacturationBundle\Entity\Compte")
     * @Groups({"default"})
     */
    protected $compteToUse;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->creances = new ArrayCollection();
        $this->rappels = new ArrayCollection();
        $this->paiements = new ArrayCollection();
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
    public function addCreance(Creance $creance)
    {
        $this->creances[] = $creance;
        $creance->setFacture($this);
        return $this;
    }

    /**
     * Remove creance.
     *
     * @param \Ovesco\FacturationBundle\Entity\Creance $creance
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeCreance(Creance $creance)
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
    public function addRappel(Rappel $rappel)
    {
        $this->rappels[] = $rappel;
        $rappel->setFacture($this);
        return $this;
    }

    /**
     * Remove rappel.
     *
     * @param \Ovesco\FacturationBundle\Entity\Rappel $rappel
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeRappel(Rappel $rappel)
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
    public function addPaiement(Paiement $paiement)
    {
        $this->paiements[] = $paiement;
        $paiement->setFacture($this);
        return $this;
    }

    /**
     * Remove paiement.
     *
     * @param \Ovesco\FacturationBundle\Entity\Paiement $paiement
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removePaiement(Paiement $paiement)
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

    /**
     * @return Compte
     */
    public function getCompteToUse()
    {
        return $this->compteToUse;
    }

    /**
     * @param Compte $compteToUse
     */
    public function setCompteToUse($compteToUse)
    {
        $this->compteToUse = $compteToUse;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }
}
