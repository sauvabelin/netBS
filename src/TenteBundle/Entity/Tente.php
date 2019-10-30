<?php

namespace TenteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use NetBS\FichierBundle\Utils\Entity\RemarqueTrait;

/**
 * Tente
 *
 * @ORM\Table(name="tente_tentes")
 * @ORM\Entity
 */
class Tente
{
    use TimestampableEntity, RemarqueTrait;

    const DISPONIBLE = 'disponible';
    const EN_ACTIVITE = 'en_activite';
    const EN_REPARATION = 'en_reparation';
    const A_REPARER = 'a_reparer';
    const INDISPONIBLE = 'indisponible';

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
     *
     * @ORM\Column(name="numero", type="string", length=255, unique=true)
     */
    private $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @var TenteModel
     *
     * @ORM\ManyToOne(targetEntity="TenteModel", inversedBy="tentes")
     */
    private $model;

    /**
     * @var FeuilleEtat[]
     *
     * @ORM\OneToMany(targetEntity="FeuilleEtat", mappedBy="tente")
     */
    private $feuillesEtat;

    /**
     * @var Reparation[]
     *
     * @ORM\OneToMany(targetEntity="Reparation", mappedBy="tente", cascade={"PERSIST", "REMOVE"})
     */
    private $reparations;

    /**
     * @var Activity[]
     *
     * @ORM\ManyToMany(targetEntity="TenteBundle\Entity\Activity", inversedBy="tentes")
     */
    private $activities;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public static function getStatutChoices() {

        return [
            self::DISPONIBLE => 'Disponible',
            self::INDISPONIBLE => 'Indisponible',
            self::A_REPARER => 'A réparer/vérifier',
            self::EN_ACTIVITE => 'En activité',
            self::EN_REPARATION => 'En réparation',
        ];
    }

    /**
     * Set numero.
     *
     * @param string $numero
     *
     * @return Tente
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero.
     *
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set status.
     *
     * @param string $status
     *
     * @return Tente
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->feuillesEtat = new \Doctrine\Common\Collections\ArrayCollection();
        $this->reparations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->status = self::DISPONIBLE;
    }

    /**
     * Set model.
     *
     * @param \TenteBundle\Entity\TenteModel|null $model
     *
     * @return Tente
     */
    public function setModel(\TenteBundle\Entity\TenteModel $model = null)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get model.
     *
     * @return \TenteBundle\Entity\TenteModel|null
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Add feuillesEtat.
     *
     * @param \TenteBundle\Entity\FeuilleEtat $feuillesEtat
     *
     * @return Tente
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
     * Add reparation.
     *
     * @param \TenteBundle\Entity\Reparation $reparation
     *
     * @return Tente
     */
    public function addReparation(\TenteBundle\Entity\Reparation $reparation)
    {
        $this->reparations[] = $reparation;
        if ($reparation->getStatus() === Reparation::EN_COURS)
            $this->setStatus(self::EN_REPARATION);
        return $this;
    }

    /**
     * Remove reparation.
     *
     * @param \TenteBundle\Entity\Reparation $reparation
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeReparation(\TenteBundle\Entity\Reparation $reparation)
    {
        return $this->reparations->removeElement($reparation);
    }

    /**
     * Get reparations.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReparations()
    {
        return $this->reparations;
    }
}
