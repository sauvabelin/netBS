<?php

namespace Ovesco\FacturationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use NetBS\FichierBundle\Utils\Entity\RemarqueTrait;
use Ovesco\FacturationBundle\Util\DebiteurTrait;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Creance
 *
 * @ORM\Table(name="ovesco_facturation_creances")
 * @ORM\Entity(repositoryClass="Ovesco\FacturationBundle\Repository\CreanceRepository")
 */
class Creance
{
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
     * @Groups({"default"})
     * @ORM\Column(name="titre", type="string", length=255)
     */
    protected $titre;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     * @Groups({"default"})
     */
    protected $date;

    /**
     * @var float
     *
     * @ORM\Column(name="montant", type="float")
     * @Groups({"default"})
     */
    protected $montant;

    /**
     * @var float
     *
     * @ORM\Column(name="rabais", type="float")
     * @Groups({"default"})
     */
    protected $rabais;

    /**
     * @var Facture
     *
     * @ORM\ManyToOne(targetEntity="Ovesco\FacturationBundle\Entity\Facture", inversedBy="creances")
     * @Groups({"creance_with_facture"})
     */
    protected $facture;

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
     * Set titre.
     *
     * @param string $titre
     *
     * @return Creance
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre.
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set montant.
     *
     * @param float $montant
     *
     * @return Creance
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
     * @return Creance
     */
    public function setFacture(\Ovesco\FacturationBundle\Entity\Facture $facture = null)
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
     * @return float
     */
    public function getRabais()
    {
        return $this->rabais;
    }

    /**
     * @param float $rabais
     */
    public function setRabais($rabais)
    {
        $this->rabais = $rabais;
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
