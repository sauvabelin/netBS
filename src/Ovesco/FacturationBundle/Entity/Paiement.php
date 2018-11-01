<?php

namespace Ovesco\FacturationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use NetBS\FichierBundle\Utils\Entity\RemarqueTrait;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Paiement
 *
 * @ORM\Table(name="ovesco_facturation_paiements")
 * @ORM\Entity(repositoryClass="Ovesco\FacturationBundle\Repository\PaiementRepository")
 */
class Paiement
{
    use TimestampableEntity, RemarqueTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var float
     *
     * @ORM\Column(name="montant", type="float")
     * @Groups({"default"})
     */
    protected $montant;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     * @Groups({"default"})
     */
    protected $date;

    /**
     * @var Facture
     *
     * @ORM\ManyToOne(targetEntity="Ovesco\FacturationBundle\Entity\Facture", inversedBy="paiements")
     * @Groups({"paiement_with_facture"})
     */
    protected $facture;

    /**
     * @var Compte
     *
     * @ORM\ManyToOne(targetEntity="Ovesco\FacturationBundle\Entity\Compte")
     * @Groups({"paiement_with_compte"})
     */
    protected $compte;

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
     * Set montant.
     *
     * @param float $montant
     *
     * @return Paiement
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;

        return $this;
    }

    /**
     * Get montant.
     *
     * @return float
     */
    public function getMontant()
    {
        return $this->montant;
    }

    /**
     * Set facture.
     *
     * @param \Ovesco\FacturationBundle\Entity\Facture|null $facture
     *
     * @return Paiement
     */
    public function setFacture(Facture $facture = null)
    {
        $this->facture = $facture;

        return $this;
    }

    /**
     * Get facture.
     *
     * @return \Ovesco\FacturationBundle\Entity\Facture|null
     */
    public function getFacture()
    {
        return $this->facture;
    }

    /**
     * @return Compte
     */
    public function getCompte()
    {
        return $this->compte;
    }

    /**
     * @param Compte $compte
     */
    public function setCompte($compte)
    {
        $this->compte = $compte;
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
